<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function setStatus($codigo) {
        $this->status()->associate(Status::where('codigo', $codigo)->first());
    }

    public function scopeVisible($query, User $user) {
        // el creador puede siempre ver sus documentos
        $query = $query->where('creador_usr_id', $user->id)

        // si el documento esta asignado a un responsable,
        // este lo podra ver si el status es pendiente-propuesta
        // y en-progreso
            ->orWhere(function($query) use ($user) {
                $query->where('responsable_usr_id', $user->id)
                      ->whereIn('status_id', [2, 4]);
            });
                       
        // si el usuario es ISM, siempre puede ver todos los documentos
        // hasta el punto en el que que el status es verificado
        if($user->hasRole("ism")) {
            $query = $query->orWhere('status_id', '<=', 5);
        }

        return $query;
    }

    public function scopeStatus($query, $codigo) {
        $status = Status::where('codigo', $codigo)->first();
        if(!$status)
            throw new \Exception("No se encontro el status con codigo $codigo");

        $query->where('status_id', $status->id);
    }

    function crear(User $user, Tipo $tipo, Departamento $departamento, $titulo, $descripcion) {
        if(!$user->hasRole('creador'))
            throw new \Exception("El usuario $user->name no puede crear documentos, no tiene el rol apropiado.");

        $this->creador_usr_id = $user->id;
        $this->setStatus('inicio');
        $this->tipo_id = $tipo->id;
        $this->departamento_id = $departamento->id;
        $this->folio = $user->contador_documentos;
        $this->fecha = Carbon::now();
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->save();

        $user->contador_documentos++;
        $user->save();
    }

    public function asignarResponsable(User $user, ?User $responsable) {
        if(!in_array($this->status->codigo, ['inicio', 'pendiente-propuesta']))
            throw new \Exception('Para asignar un responsable, el documento tiene que esta al inicio de su proceso o estar pendiente de una propuesta.');

        if(!$user->hasRole('ism'))
            throw new \Exception("El usuario $user->name no puede asignar responsables, no tiene el rol apropiado.");
        if(!$responsable) {
            $this->responsable()->dissociate();
            $this->setStatus('inicio');
            return;
        }

        if(!$responsable->hasRole('responsable'))
            throw new \Exception("El usuario $responsable->name no puede encargarse de este documento, on tiene el rol apropiado.");

        $this->responsable()->associate($responsable);
        $this->setStatus('pendiente-propuesta');
    }

    public function agregarPropuesta(User $user, $descripcion) {
        if(!$doc->responsable_usr_id)
            throw new \Exception('En este momento nadie puede agregar propuestas a este documento.');
        elseif($user->id != $this->responsable_usr_id)
            throw new \Exception("Solo {$this->responsable->name} puede agregar propuestas a este documento.");

        if($this->status->codigo != 'pendiente-propuesta') 
            throw new \Exception('Solo se puede agregar una propuesta a aquellos documentos que esten esperando una propuesta');

        if(!$user->hasRole('responsable'))
            throw new \Exception("El usuario $user->name no puede agregar propuestas, no tiene el rol apropiado.");

        $propuesta = new Propuesta;
        $propuesta->responsable()->associate($user);
        $propuesta->descripcion = $descripcion;

        $this->propuestas()->save($propuesta);

        $this->setStatus('pendiente-revision');
        return $propuesta;
    }

    public function rechazarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        if(!$user->hasRole('ism')) 
            throw new \Exception("El usuario $user->name no puede rechazar propuestas, no tiene el rol apropiado.");

        if($this->status->codigo != 'pendiente-revision')
            throw new \Exception('Solo se puede rechazar propuestas cuando estan pendientes de revisión');

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception("Solo se puede aceptar la ultima propuesta del documento, ");

        $propuesta->retroalimentador()->associate($user);
        $propuesta->retro = $comentarios;

        $this->setStatus('inicio');
    }

    public function aceptarPropuesta(User $user, Propuesta $propuesta, $comentarios) {
        if(!$user->hasRole('ism'))
            throw new \Exception("Solo ISM puede aceptar propuestas");

        if($this->status->codigo != 'pendiente-revision')
            throw new \Exception('Solo se puede aceptar propuestas cuando estan pendientes de revisión');

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception('Solo se puede aceptar la ultima propuesta del documento');

        $propuesta->retroalimentador()->associate($user);
        $propuesta->retro = $comentarios;

        $this->setStatus('en-progreso');
    } 

    public function corregido(User $user) {
        if($user->id != $this->responsable_usr_id)
            throw new \Exception("Solo el responsable pude marcar el documento como corregido");

        if($this->status->codigo != 'en-progreso')
            throw new \Exception("Solo se pueden marcar como corregidos aquellos documentos que esten en progreso.");

        if(!$user->hasRole('responsable'))
            throw new \Exception("El usuario $user->name no puede marcar el documento como corregido, no tiene el rol apropiado");

        $this->setStatus('corregido');
    }

    public function verificado(User $user) {
        if($user->id != $this->creador_usr_id) 
            throw new \Exception("Solo {$this->creador->name} puede marcar este documento como verificado");

        if($this->status->codigo != 'corregido')
            throw new \Exception("Solo se pueden marcar como verificado aquellos documentos que esten marcados como corregidos.");


        $this->setStatus('verificado');
    }

    public function cerrar(User $user) {
        if($user->id != $this->creador_usr_id) 
            throw new \Exception("Solo {$this->creador->name} puede cerrar este documento.");

        if($this->status->codigo != 'verificado')
            throw new \Exception("Solo se pueden cerrar aquellos documentos que esten marcados como verificados.");

        $this->setStatus('cerrado');
    }
}
