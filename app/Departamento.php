<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    public function users() {
        return $this->hasMany('App\User');
    }

    public function documentos() {
        return $this->hasMany('App\Documento');
    }
}
