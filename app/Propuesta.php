<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Propuesta extends Model
{
    protected $table = 'propuestas';

    public function responsable() {
        return $this->belongsTo('App\User', 'responsable_usr_id');
    }

    public function retroalimentador() {
        return $this->belongsTo('App\User', 'retro_usr_id');
    }

    public function documento() {
        return $this->belongsTo('App\Documento', 'documento_id');
    }

}