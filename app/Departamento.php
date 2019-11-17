<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = ['nombre'];


    public function documentos() {
        return $this->hasMany('App\Documento');
    }

    public function users() {
        return $this->belongsToMany('App\User', 'user_departamentos');
    }
}
