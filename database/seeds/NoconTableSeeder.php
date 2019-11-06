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
        $departamento1 = Departamento::create([
            'nombre'=> 'Empresa 1, Barcos I'
        ]);
        $departamento2 = Departamento::create([
            'nombre' => 'Empresa 1, Barco II'
        ]);
        $departamento3 = Departamento::create([
            'nombre' => 'Empresa 2, Barco X'
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
            'name' => 'director',
        ]);

        Role::create([
            'name' => 'responsable'
        ]);

        Role::create([
            'name' => 'creador'
        ]);

        /* Creadores */
        $user = User::create([
            'name'=>'Creador1',
            'email'=>'creador1@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento1->id,
            'serie_documentos'=>'MX1'
        ]);
        $user->addRole('creador');
        $user->departamentos()->attach($departamento1->id);
        $user->departamentos()->attach($departamento2->id);
        $user = User::create([
            'name'=>'Creador2',
            'email'=>'creador2@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento3->id,
            'serie_documentos'=>'ABC'
        ]);
        $user->addRole('creador');
        $user->departamentos()->attach($departamento3->id);

        /* Directores */
        $user = User::create([
            'name'=>'Director1',
            'email'=>'director1@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento1->id,
        ]);
        $user->addRole('director');
        $user->departamentos()->attach($departamento1->id);
        $user->departamentos()->attach($departamento2->id);
        $user = User::create([
            'name'=>'Director2',
            'email'=>'director2@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento3->id,
        ]);
        $user->addRole('director');
        $user->departamentos()->attach($departamento3->id);

        /* Responsables */
        $user = User::create([
            'name'=>'Responsable1',
            'email'=>'responsable1@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento1->id,
          ]);
        $user->addRole('responsable');
        $user->departamentos()->attach($departamento1->id);
        $user->departamentos()->attach($departamento2->id);

        $user = User::create([
            'name'=>'Responsable2',
            'email'=>'responsable2@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$departamento3->id,
          ]);
        $user->addRole('responsable');
        $user->departamentos()->attach($departamento3->id);

        $user = User::create([
            'name' =>'Admin',
            'email'=>'admin@nocon.com',
            'password'=>Hash::make('admin'),
        ]);
        $user->addRole('admin');
        $user->addRole('creador');
        $user->addRole('director');
        $user->addRole('responsable');
        $user->departamentos()->attach($departamento1->id);
        $user->departamentos()->attach($departamento2->id);
        $user->departamentos()->attach($departamento3->id);
    }
}
