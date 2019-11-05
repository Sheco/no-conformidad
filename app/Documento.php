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

    public function puedeAvanzar(User $user) {
        $politicasAvance = [
            'inicio'=>'asignarResponsable',
            'pendiente-responsable'=>'agregarPropuesta',
            'pendiente-revision'=>'aceptarPropuesta',
            'en-progreso'=>'corregir',
            'corregido'=>'verificar',
            'verificado'=>'cerrar',
            'cerrado'=>''
        ];
        return Gate::allows($politicasAvance[$this->status->codigo], $this);
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

    function getFechaLimiteDiffAttribute() {
        $limite = new Carbon($this->fecha_limite);
        $now = Carbon::now();
        if($now >= $limite) {
            return CarbonInterval::hours(0);
        }
        return $limite->diffAsCarbonInterval($now);
    }
    function getFechaLimiteDiffForHumansAttribute() {
        $diff = $this->fechaLimiteDiff;
        if($diff->seconds == 0) return "Vencido";
        return $diff->forHumans(['parts'=>2]);
    }

    function crear(User $user, Tipo $tipo, Departamento $departamento, $titulo, $descripcion) {
        Gate::forUser($user)->authorize('crear', Documento::class);
        if(!$user->departamentos()->where('id', $departamento->id)->exists()) {
            throw new \Exception("El usuario $user->name no esta suscrito al departamento $departamento->nombre");
        }
        $this->creador_usr_id = $user->id;
        $this->setStatus('inicio');
        $this->tipo_id = $tipo->id;
        $this->departamento_id = $departamento->id;
        $year = date('y');
        $this->folio = "$user->serie_documentos $user->contador_documentos/$year";
        $this->fecha_limite = Carbon::now()->addDays(1);
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->save();

        $user->contador_documentos++;
        $user->save();
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
        $this->fecha_limite = Carbon::now()->addDays(3);
    }

    public function agregarPropuesta(User $user, $descripcion) {
        Gate::forUser($user)->authorize('agregarPropuesta', $this);
        return DB::transaction(function() use ($user, $descripcion) {
            $propuesta = new Propuesta;
            $propuesta->responsable()->associate($user);
            $propuesta->descripcion = $descripcion;

            $this->propuestas()->save($propuesta);

            $this->setStatus('pendiente-revision');
            $this->fecha_limite = Carbon::now()->addDays(90);
            return $propuesta;
        });
    }

    public function rechazarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        Gate::forUser($user)->authorize('rechazarPropuesta', $this);

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception("Solo se puede aceptar la ultima propuesta del documento, ");

        return DB::transaction(function() use ($propuesta, $user, $comentarios) {
            $propuesta->retroalimentador()->associate($user);
            $propuesta->retro = $comentarios;
            $propuesta->status = false;

            $this->setStatus('pendiente-propuesta');
        });
    }

    public function aceptarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        Gate::forUser($user)->authorize('aceptarPropuesta', $this);

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception('Solo se puede aceptar la ultima propuesta del documento');

        return DB::transaction(function() use ($propuesta, $user, $comentarios) {
            $propuesta->retroalimentador()->associate($user);
            $propuesta->retro = $comentarios;
            $propuesta->status = true;

            $this->setStatus('en-progreso');
        });
    } 

    public function corregir(User $user) {
        Gate::forUser($user)->authorize('corregir', $this);
        $this->setStatus('corregido');
    }

    public function verificar(User $user) {
        Gate::forUser($user)->authorize('verificar', $this);
        $this->setStatus('verificado');
    }

    public function cerrar(User $user) {
        Gate::forUser($user)->authorize('cerrar', $this);
        $this->setStatus('cerrado');
    }
}
