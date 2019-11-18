<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
        $filtros = session('filtros', []);
        $filtrosSerializados = serialize($filtros);
        $cache_key = "status($this->id)->documentosVisibles($user->id)($filtrosSerializados)";

        return Cache::store('file')
            ->remember($cache_key, 60, function() use ($user, $filtros) {

            return Documento::visible($user)
                ->filtrados($filtros)
                ->statusId($this->id)
                ->count();
        });
    }

    public function documentosVisiblesBadge(User $user) {
        if(!$this->codigo) 
          return "<span class=\"badge badge-light\">-</span>";

        $total = $this->documentosVisibles($user);
        if(!$total)
          return "<span class=\"badge badge-light\">0</span>";

        else return "<span class=\"badge badge-danger text-light\">$total</span>";
    }

    public function getNombreColoreadoAttribute() {
        return "<span class=\"bg-light status-$this->codigo\">$this->nombre</span>";
    }


    static function wildcard() {
        $instance = new self;
        $instance->codigo = "";
        $instance->nombre = "Cualquiera";
        return $instance;
    }
}
