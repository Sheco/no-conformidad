<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Status;
use App\Tipo;
use App\Departamento;
use App\Events\DocumentoActualizado;

class Documento extends Model
{
    protected $table = 'documentos';

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

    public function puedeAvanzar(User $user) {
        $politicasAvance = [
            'inicio'=>'asignarResponsable',
            'pendiente-propuesta'=>'agregarPropuesta',
            'pendiente-revision'=>'aceptarPropuesta',
            'en-progreso'=>'corregir',
            'corregido'=>'verificar',
            'verificado'=>'cerrar',
            'cerrado'=>''
        ];
        return Gate::allows($politicasAvance[$this->status->codigo], $this);
    }

    public function getTienePropuestasAttribute() {
        return $this->propuestas->count() > 0;
    }

    public function setStatus($codigo) {
        $this->status()->associate(Status::where('codigo', $codigo)->first());
    }

    public function scopeVisible($query, User $user) {
        // el creador puede siempre ver sus documentos
        $query->whereIn('departamento_id', $user->departamentos->pluck('id'))
           ->where(function($query) use ($user) {
               $query->where('creador_usr_id', $user->id)

            // si el documento esta asignado a un responsable,
            // este lo podra ver si el status es pendiente-propuesta
            // y en-progreso
                ->orWhere(function($query) use ($user) {
                    $query->where('responsable_usr_id', $user->id)
                          ->whereIn('status_id', [2, 4]);
                });
                           
            // si el usuario es director, siempre puede ver todos los documentos
            // hasta el punto en el que que el status es verificado
            if($user->hasRole("director")) {
                $query->orWhere('status_id', '<=', 5);
            }
        });

    }

    public function scopeStatus($query, $codigo) {
        $status = Status::where('codigo', $codigo)->first();
        if(!$status)
            throw new \Exception("No se encontro el status con codigo $codigo");

        $query->where('status_id', $status->id);
    }

    function getFechaEntregaAttribute() {
        $propuesta = $this->propuestas->where('status', 1)->last();
        return $propuesta
            ?new Carbon($propuesta->fecha_entrega)
            :new Carbon($this->fecha_maxima);
    }
    function getFechaMaximaDiffAttribute() {
        $fecha = $this->fechaEntrega;
        $now = Carbon::now();
        if($now >= $fecha) {
            return CarbonInterval::hours(0);
        }
        return $fecha->diffAsCarbonInterval($now);
    }

    function getFechaMaximaDiffForHumansAttribute() {
        if(!$this->fecha_maxima) return "N/A";
        $diff = $this->fechaMaximaDiff;
        if($diff->seconds == 0) return "Vencido";
        return $diff->forHumans(['parts'=>2]);
    }

    function crear(User $user, Tipo $tipo, Departamento $departamento, $titulo, $descripcion) {
        Gate::forUser($user)->authorize('crear', Documento::class);

        if(!$user->departamentos()->where('id', $departamento->id)->exists()) {
            throw new \Exception("El usuario $user->name no esta suscrito al departamento $departamento->nombre");
        }

        $year = date('y');

        $this->creador_usr_id = $user->id;
        $this->setStatus('inicio');
        $this->tipo_id = $tipo->id;
        $this->departamento_id = $departamento->id;
        $this->folio = "$user->serie_documentos $user->contador_documentos/$year";
        $this->fecha_maxima = Carbon::now()->addDays(1);
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->save();

        $user->contador_documentos++;
        $user->save();

        event(new DocumentoActualizado($this, $user, 'crear'));
    }

    public function asignarResponsable(User $user, ?User $responsable) {
        Gate::forUser($user)->authorize('asignarResponsable', $this);

        if(!$responsable) {
            $this->responsable()->dissociate();
            $this->setStatus('inicio');
            return;
        }

        if(!$responsable->hasRole('responsable'))
            throw new \Exception("El usuario $responsable->name no puede encargarse de este documento, on tiene el rol apropiado.");

        if(!$user->departamentos()->where('id', $responsable->departamento_id)->exists()) {
            throw new \Exception("El usuario $user->name no puede asignar al usuario $responsable->name, pues no esta suscrito al departamento {$responsable->departamento->nombre}");
        }

        $this->responsable()->associate($responsable);
        $this->setStatus('pendiente-propuesta');

        if(!$this->tienePropuestas) {
            $this->fecha_maxima = Carbon::now()->addDays(3);
        }
        $this->save();

        event(new DocumentoActualizado($this, $user, 'asignarResponsable'));
    }

    public function agregarPropuesta(User $user, $descripcion, $fecha_entrega) {
        Gate::forUser($user)->authorize('agregarPropuesta', $this);

        $fecha_entrega = new Carbon("$fecha_entrega 12:00:00");
        $fecha_maxima = !$this->tienePropuestas?
            Carbon::now()->addDays(90):
            $this->fecha_maxima;

        if($fecha_entrega > $fecha_maxima)
            throw new \Exception("La fecha propuesta de entrega no puede exceder la fecha maxima de ". $this->fecha_maxima);

        return DB::transaction(function() use ($user, $descripcion, $fecha_entrega, $fecha_maxima) {
            $propuesta = new Propuesta;
            $propuesta->responsable()->associate($user);
            $propuesta->descripcion = $descripcion;
            $propuesta->fecha_entrega = $fecha_entrega;

            $this->propuestas()->save($propuesta);

            $this->setStatus('pendiente-revision');
            $this->fecha_maxima = $fecha_maxima;
            $this->save();

            event(new DocumentoActualizado($this, $user, 'agregarPropuesta'));
            return $propuesta;
        });
    }

    public function rechazarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        Gate::forUser($user)->authorize('rechazarPropuesta', $this);

        if($this->propuestas->last()->id != $propuesta->id)
            throw new \Exception("Solo se puede aceptar la ultima propuesta del documento, ");

        return DB::transaction(function() use ($propuesta, $user, $comentarios) {
            $propuesta->retroalimentador()->associate($user);
            $propuesta->retro = $comentarios;
            $propuesta->status = false;
            $propuesta->save();

            $this->setStatus('pendiente-propuesta');
            $this->save();

            event(new DocumentoActualizado($this, $user, 'rechazarPropuesta'));
        });
    }

    public function aceptarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        Gate::forUser($user)->authorize('aceptarPropuesta', $this);

        if($this->propuestas->last()->id != $propuesta->id)
            throw new \Exception('Solo se puede aceptar la ultima propuesta del documento');

        return DB::transaction(function() use ($propuesta, $user, $comentarios) {
            $propuesta->retroalimentador()->associate($user);
            $propuesta->retro = $comentarios;
            $propuesta->status = true;
            $propuesta->save();

            $this->setStatus('en-progreso');
            $this->save();

            event(new DocumentoActualizado($this, $user, 'aceptarPropuesta'));
        });
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
        $this->fecha_maxima = null;
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
