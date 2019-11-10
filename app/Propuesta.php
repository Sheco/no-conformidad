<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

}
