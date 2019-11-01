<?php

use Illuminate\Database\Seeder;

use App\Departamento;
use App\Tipo;
use App\Status;
use App\User;

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
            
        User::create([
            'name'=>'Barco',
            'email'=>'barco@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$barco->id,
        ]);
        User::create([
            'name'=>'ISM',
            'email'=>'ism@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$ism->id,
        ]);
        User::create([
            'name'=>'Responsable',
            'email'=>'responsable@nocon.com',
            'password'=>Hash::make('nocon'),
            'departamento_id'=>$responsable->id,
        ]);
    }
}
