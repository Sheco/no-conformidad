<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'departamento_id',
        'serie_documentos', 'contador_documentos',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function departamento() {
        return $this->belongsTo('App\Departamento');
    }

    public function roles() {
        return $this->belongsToMany('App\Role', 'user_roles');
    }

    public function departamentos() {
        return $this->belongsToMany('App\Departamento', 'user_departamentos');
    }

    public function logs() {
        return $this->hasMany('App\DocumentoLog');
    }

    public function addRole($name) {
        $role = Role::where('name', $name)->first();
        $this->roles()->attach($role->id);
    }

    public function delRole($name) {
        $role = Role::where('name', $name)->first();
        $this->roles()->dettach($role->id);
    }

    public function hasRole($name) {
        return Cache::store('file')
            ->remember("user($this->id)->hasRole($name)", 60, 
            function() use ($name) {
            return $this->roles()->where('name', $name)->exists();
        });
    }

    public function setPasswordAttribute($password) {
        if(!$password) 
            return;
        $this->attributes['password'] = Hash::make($password);
    }
}
