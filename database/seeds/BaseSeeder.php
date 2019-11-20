<?php

use Illuminate\Database\Seeder;
use App\Departamento;
use App\Tipo;
use App\Status;
use App\User;
use App\Role;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
            'name' => 'director',
        ]);

        Role::create([
            'name' => 'responsable'
        ]);

        Role::create([
            'name' => 'creador'
        ]);

        $user = User::create([
            'name' =>'Admin',
            'email'=>'admin@localhost',
            'password'=>'admin',
            'serie_documentos'=>'ADM',
        ]);
        $user->addRole('admin');
        $user->addRole('creador');
        $user->addRole('director');
        $user->addRole('responsable');
    }
}
