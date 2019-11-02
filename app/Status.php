<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    static function codigo($codigo) {
        return self::where('codigo', $codigo)->first();
    }

    public function documentos() {
        return $this->hasMany('App\Documento');
    }
}