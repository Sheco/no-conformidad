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
        return Documento::visible($user)
            ->statusId($this->id)
            ->count();
    }

    public function documentosVisiblesBadge(User $user) {
        $total = $this->documentosVisibles($user);
        if(!$total)
            return "<span class=\"badge badge-light\">0</span>";
        else return "<span class=\"badge badge-danger text-light\">$total</span>";
    }

    public function getNombreColoreadoAttribute() {
        return "<span class=\"bg-light status-$this->codigo\">$this->nombre</span>";
    }
}
