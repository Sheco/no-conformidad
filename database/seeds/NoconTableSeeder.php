<?php

use Illuminate\Database\Seeder;

use App\Departamento;
use App\Tipo;
use App\Status;
use App\User;
use App\Role;

use Illuminate\Support\Facades\Hash;

class NoconTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $barco = Departamento::create([
            'nombre'=> 'Barco I'
        ]);
        $ism = Departamento::create([
            'nombre' => 'ISM'
        ]);
        $responsable = Departamento::create([
            'nombre' => 'Responsables de acción correctiva'
        ]);

        Tipo::create([ 
            'nombre' => 'Incumplimiento' 
        ]);
        Tipo::create([ 
            'nombre' => 'Situación de riesgo' 
        ]);
        Tipo::create([ 
            'nombre' => 'Accidente / Avería' 
        ]);

        Status::create([ 
            'codigo'=> 'inicio', 
            'nombre'=>'Inicio' ]
        );
        Status::create([ 
            'codigo'=> 'pendiente-propuesta', 
            'nombre'=>'Pendiente propuesta'
        ]);
        Status::create([ 
            'codigo'=> 'pendiente-revision', 
            'nombre'=>'Pendiente revisión' 
        ]);
        Status::create([ 
            'codigo'=> 'en-progreso', 
            'nombre'=>'En progreso' 
        ]);
        Status::create([ 
            'codigo'=> 'corregido', 
            'nombre'=>'Corregido' 
        ]);
        Status::create([ 
            'codigo'=> 'verificado', 
            'nombre'=>'Verificado' 
        ]);
        Status::create([ 
            'codigo'=> 'cerrado', 
            'nombre'=>'Cerrado' 
        ]);

        Role::create([
            'name'=>'admin',
        ]);

        Role::create([
            'name' => 'ism',
        ]);

        Role::create([
            'name' => 'responsable'
        ]);

        Role::create([
            'name' => 'creador'
        ]);
            
        $user = User::create([
            'name'=>'Barco',
            'email'=>'barco@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$barco->id,
        ]);
        $user->addRole('creador');

        $user = User::create([
            'name'=>'ISM',
            'email'=>'ism@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$ism->id,
        ]);
        $user->addRole('ism');

        $user = User::create([
            'name'=>'Responsable1',
            'email'=>'responsable1@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$responsable->id,
          ]);
        $user->addRole('responsable');
        $user = User::create([
            'name'=>'Responsable2',
            'email'=>'responsable2@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$responsable->id,
          ]);
        $user->addRole('responsable');
    }
}
