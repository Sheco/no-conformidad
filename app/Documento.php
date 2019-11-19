<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

use App\Status;
use App\Tipo;
use App\Departamento;
use App\Events\DocumentoActualizado;

use Arr;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $dates =  [ 'limite_maximo', 'limite_actual' ];

    public function status() {
        return $this->belongsTo('App\Status');
    }

    public function tipo() {
        return $this->belongsTo('App\Tipo');
    }

    public function departamento() {
        return $this->belongsTo('App\Departamento');
    }

    public function propuestas() {
        return $this->hasMany('App\Propuesta');
    }

    public function creador() {
        return $this->belongsTo('App\User', 'creador_usr_id');
    }

    public function responsable() {
        return $this->belongsTo('App\User', 'responsable_usr_id');
    }

    public function logs() {
        return $this->hasMany('App\DocumentoLog');
    }

    public function archivos() {
        return $this->hasMany('App\DocumentoArchivo');
    }

    public function directores() {
        return User::whereHas('departamentos', function($q) { 
            $q->where('id', $this->departamento_id); 
        })->whereHas('roles', function($q) { 
            $q->where('name', 'director'); 
        })->get();
    }

    public function puedeAvanzar(User $user) {
        $politicasAvance = [
            'inicio'=>'asignarResponsable',
            'pendiente-propuesta'=>'agregarPropuesta',
            'pendiente-revision'=>'aceptarPropuesta',
            'en-progreso'=>'corregir',
            'corregido'=>'verificar',
            'verificado'=>'cerrar',
        ];

        if($this->status->codigo == 'cerrado')
            return false;

        return Gate::allows($politicasAvance[$this->status->codigo], $this);
    }

    public function getTienePropuestasAttribute() {
        return $this->propuestas->count() > 0;
    }

    public function setStatus($codigo) {
        $this->status()->associate(Status::where('codigo', $codigo)->first());
    }

    public function scopeVisible($query, User $user) {
        // solo se pueden ver documentos de los departamentos a los que uno
        // pertenece.
        $departamentos = Cache::store('file')->remember("user($user->id)->departamentos", 60, function() use ($user) {
            return $user->departamentos->pluck('id');
        });
        $query->whereIn('departamento_id', $departamentos)
           ->where(function($query) use ($user) {
                // el creador puede siempre ver sus documentos
                $query->where('creador_usr_id', $user->id)

                // si el documento esta asignado a un responsable,
                // este lo podra ver si el status es pendiente-propuesta
                // y en-progreso
                ->orWhere(function($query) use ($user) {
                    $query->where('responsable_usr_id', $user->id)
                          ->whereIn('status_id', [2, 4]);
                });
                           
                // si el usuario es director, siempre puede ver todos los 
                // documentos hasta el punto en el que que el status es 
                // verificado
                if($user->hasRole("director")) {
                    $query->orWhere('status_id', '<=', 5);
                }

                if($user->hasRole("admin")) {
                    $query->orWhere('status_id', '<=', 7);
                }
        });

    }

    function scopeFiltrados($query, $filtros) {
        if(Arr::get($filtros, 'creador_usr_id', null)) {
            $query->where('creador_usr_id', 
                $filtros['creador_usr_id']);
        }
        if(Arr::get($filtros, 'departamento_id', null)) {
            $query->where('departamento_id', 
                $filtros['departamento_id']);
        }
        if(Arr::get($filtros, 'tipo_id', null)) {
            $query->where('tipo_id', 
                $filtros['tipo_id']);
        }
    }

    public function scopeStatus($query, $codigo) {
        if(!$codigo) 
            return;

        $status = Status::where('codigo', $codigo)->first();
        if(!$status)
            throw new \Exception("No se encontro el status con codigo $codigo");

        $query->where('status_id', $status->id);
    }

    public function scopeStatusId($query, $id) {
        $query->where('status_id', $id);
    }

    function getTiempoLimiteAttribute() {
        $fecha = $this->limite_actual;
        $now = Carbon::now();
        if($now >= $fecha) {
            return CarbonInterval::hours(0);
        }
        return $fecha->diffAsCarbonInterval($now);
    }

    function getTiempoLimiteLegibleAttribute() {
        if(!$this->limite_actual) return "N/A";
        $diff = $this->tiempoLimite;
        if($diff->seconds == 0) return "Vencido";
        return $diff->forHumans(['parts'=>2]);
    }

    function getLimiteMaximoPropuestaAttribute() {
        if($this->tienePropuestas)
            return $this->limite_maximo;

        return Carbon::now()->addDays(90);
    }

    function crear(User $user, Tipo $tipo, Departamento $departamento, $titulo, $descripcion, ?UploadedFile $archivo = null) {
        Gate::forUser($user)->authorize('crear', Documento::class);

        if(!$user->departamentos()->where('id', $departamento->id)->exists()) {
            throw new \Exception("El usuario $user->name no esta suscrito al departamento $departamento->nombre");
        }

        DB::transaction(function() 
            use ($user, $tipo, $departamento, $titulo, $descripcion, $archivo) {
            $year = date('y');

            $this->creador_usr_id = $user->id;
            $this->setStatus('inicio');
            $this->tipo_id = $tipo->id;
            $this->departamento_id = $departamento->id;
            $this->folio = "$user->serie_documentos $user->contador_documentos/$year";
            $this->limite_maximo = Carbon::now()->addDays(1);
            $this->limite_actual = $this->limite_maximo;
            $this->titulo = $titulo;
            $this->descripcion = $descripcion;
            $this->save();

            $user->contador_documentos++;
            $user->save();

            if($archivo && $archivo->isValid()) {
                $this->guardarArchivo($user, $archivo);
            }
        });
        event(new DocumentoActualizado($this, $user, 'crear'));
    }

    function guardarArchivo(User $user, UploadedFile $archivo) {
        return DB::transaction(function()  use ($user, $archivo) {
            $docarc = new DocumentoArchivo;
            $docarc->nombre = $archivo->getClientOriginalName();
            $docarc->user_id = $user->id;
            $this->archivos()->save($docarc);

            $archivo->storeAs('documentos', $docarc->id);
        });
    }

    public function asignarResponsable(User $user, ?User $responsable) {
        Gate::forUser($user)->authorize('asignarResponsable', $this);

        if(!$responsable) {
            $this->responsable()->dissociate();
            $this->setStatus('inicio');
            return;
        }

        if(!$responsable->hasRole('responsable'))
            throw new \Exception("El usuario $responsable->name no puede encargarse de este documento, no tiene el rol apropiado.");

        if(!$responsable->departamentos
            ->where('id', $this->departamento_id) 
            ->count()) {
            throw new \Exception("El usuario $responsable->name no puede encargarse de este documento, no tiene el departamento {$this->departamento->nombre}");
        }

        DB::transaction(function() use ($responsable) {
            $this->responsable()->associate($responsable);
            $this->setStatus('pendiente-propuesta');

            if(!$this->tienePropuestas) {
                $this->limite_maximo = Carbon::now()->addDays(3);
                $this->limite_actual = $this->limite_maximo;
            }
            $this->save();
        });

        event(new DocumentoActualizado($this, $user, 'asignarResponsable'));
    }

    public function agregarPropuesta(User $user, $descripcion, $fecha_entrega) {
        Gate::forUser($user)->authorize('agregarPropuesta', $this);

        $fecha_entrega = new Carbon("$fecha_entrega 17:00:00");
        $limite_maximo = $this->limiteMaximoPropuesta;

        if($fecha_entrega > $limite_maximo)
            throw new \Exception("La fecha propuesta de entrega no puede exceder la fecha maxima de ". $limite_maximo);

        $propuesta = DB::transaction(function() 
            use ($user, $descripcion, $fecha_entrega, $limite_maximo) {
            $propuesta = new Propuesta;
            $propuesta->responsable()->associate($user);
            $propuesta->descripcion = $descripcion;
            $propuesta->fecha_entrega = $fecha_entrega;

            $this->propuestas()->save($propuesta);

            $this->setStatus('pendiente-revision');
            $this->limite_maximo = $limite_maximo;
            $this->save();

            return $propuesta;
        });

        event(new DocumentoActualizado($this, $user, 'agregarPropuesta', $propuesta));

        return $propuesta;
    }

    public function corregir(User $user) {
        Gate::forUser($user)->authorize('corregir', $this);

        $this->setStatus('corregido');
        $this->save();

        event(new DocumentoActualizado($this, $user, 'corregir'));
    }

    public function verificar(User $user) {
        Gate::forUser($user)->authorize('verificar', $this);

        $this->setStatus('verificado');
        $this->limite_actual = null;
        $this->save();

        event(new DocumentoActualizado($this, $user, 'verificar'));
    }

    public function cerrar(User $user) {
        Gate::forUser($user)->authorize('cerrar', $this);

        $this->setStatus('cerrado');
        $this->save();

        event(new DocumentoActualizado($this, $user, 'cerrar'));
    }
}
