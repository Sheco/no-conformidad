<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Status;
use App\Tipo;

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
        if($user->departamento->nombre == "ISM") {
            $query = $query->orWhere('status_id', '<=', 5);
        }

        return $query;
    }

    static function nuevo(User $user, Tipo $tipo, $descripcion) {
        $nueva = new self;
        $nueva->creador_usr_id = $user->id;
        $nueva->setStatus('inicio');
        $nueva->tipo_id = $tipo->id;
        $nueva->folio = $user->contador_documentos;
        $nueva->fecha = Carbon::now();
        $nueva->descripcion = $descripcion;
        $nueva->save();

        $user->contador_documentos++;
        $user->save();
        return $nueva;
    }

    public function asignarResponsable(User $user) {
        if($this->status->codigo != 'inicio')
            throw new \Exception('Para asignar un responsable, el documento tiene que esta al inicio de su proceso');

        if($this->status->codigo != 'inicio')
            throw new \Exception('Solo puede asignarse un responsable a aquellos documentos que esten al inicio del proceso');

        $this->responsable()->associate($user);
        $this->setStatus('pendiente-propuesta');
    }

    public function agregarPropuesta(User $user, $descripcion) {
        if($user->id != $this->responsable_usr_id)
            throw new \Exception("Solo el responsable del documento puede agregar propuestas.");

        if($this->status->codigo != 'pendiente-propuesta') 
            throw new \Exception('Solo se puede agregar una propuesta a aquellos documentos que esten esperando una propuesta');

        $propuesta = new Propuesta;
        $propuesta->responsable()->associate($user);
        $propuesta->descripcion = $descripcion;

        $this->propuestas()->save($propuesta);

        $this->setStatus('pendiente-revision');
        return $propuesta;
    }

    public function rechazarPropuesta(Propuesta $propuesta, User $ism, $comentarios) {
        if($ism->departamento->nombre != "ISM") 
            throw new \Exception("Solo ISM puede rechazar propuestas");

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception('Solo se puede aceptar la ultima propuesta del documento');

        $propuesta->retroalimentador()->associate($ism);
        $propuesta->retro = $comentarios;

        $this->setStatus('inicio');
    }

    public function aceptarPropuesta(Propuesta $propuesta, User $ism, $comentarios) {
        if($ism->departamento->nombre != "ISM")
            throw new \Exception("Solo ISM puede aceptar propuestas");

        if($this->propuestas()->get()->last()->id != $propuesta->id)
            throw new \Exception('Solo se puede aceptar la ultima propuesta del documento');

        $propuesta->retroalimentador()->associate($ism);
        $propuesta->retro = $comentarios;

        $this->setStatus('en-progreso');
    } 

    public function corregido(User $responsable) {
        if($responsable->id != $this->responsable_usr_id)
            throw new \Exception("Solo el responsable pude marcar el documento como corregido");

        if($this->status->codigo != 'en-progreso')
            throw new \Exception("Solo se pueden marcar como corregido aquellos documentos que esten en progreso.");

        $this->setStatus('corregido');
    }

    public function verificado(User $creador) {
        if($creador->id != $this->creador_usr_id) 
            throw new \Exception("Solo el creador puede marcar el documento como verificado");

        if($this->status->codigo != 'corregido')
            throw new \Exception("Solo se pueden marcar como verificado aquellos documentos que esten marcados como corregidos.");

        $this->setStatus('verificado');
    }

    public function cerrar(User $creador) {
        if($creador->id != $this->creador_usr_id) 
            throw new \Exception("Solo el creador puede cerrar el documento.");

        if($this->status->codigo != 'verificado')
            throw new \Exception("Solo se pueden cerrar aquellos documentos que esten marcados como verificados.");

        $this->setStatus('cerrado');
    }
}
