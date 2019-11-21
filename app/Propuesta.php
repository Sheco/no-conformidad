<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Events\DocumentoActualizado;

class Propuesta extends Model
{
    protected $table = 'propuestas';
    protected $dates = ['fecha_entrega'];

    public function responsable() {
        return $this->belongsTo('App\User', 'responsable_usr_id');
    }

    public function retroalimentador() {
        return $this->belongsTo('App\User', 'retro_usr_id');
    }

    public function documento() {
        return $this->belongsTo('App\Documento', 'documento_id');
    }

    public function getHeaderStyleAttribute() {
      if($this->status === "1") return " bg-success text-light";
      else if($this->status === "0") return " bg-danger text-light";
    }

    public function rechazar(User $user, $comentarios) {
        Gate::forUser($user)->authorize('rechazar', $this);

        DB::transaction(function() use ($user, $comentarios) {
            $this->retroalimentador()->associate($user);
            $this->retro = $comentarios;
            $this->status = false;
            $this->save();

            $this->documento->setStatus('pendiente-propuesta');
            $this->documento->save();
        });

        event(new DocumentoActualizado($this->documento, $user, 'rechazarPropuesta', $this));
    }

    public function aceptar(User $user, $comentarios) {
        Gate::forUser($user)->authorize('aceptar', $this);

        DB::transaction(function() use ($user, $comentarios) {
            $this->retroalimentador()->associate($user);
            $this->retro = $comentarios;
            $this->status = true;
            $this->save();

            $this->documento->setStatus('en-progreso');
            $this->documento->limite_actual = $this->fecha_entrega;
            $this->documento->save();
        });

        event(new DocumentoActualizado($this->documento, $user, 'aceptarPropuesta', $this));
    } 

}
