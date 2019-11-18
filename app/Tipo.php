<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'tipos';
    protected $fillable = ['nombre'];

    public function documentos() {
        return $this->hasMany('App\Documento');
    }
}
