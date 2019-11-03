<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Documento;

class Status extends Model
{
    protected $table = 'status';

    static function codigo($codigo) {
        return self::where('codigo', $codigo)->first();
    }

    public function documentos() {
        return $this->hasMany('App\Documento');
    }

    public function documentosVisibles(User $user) {
        return Documento::visible($user)->status($this->codigo)->count();
    }

    public function getNombreColoreadoAttribute() {
        return "<span class=\"status-$this->codigo\">$this->nombre</span>";
    }
}
