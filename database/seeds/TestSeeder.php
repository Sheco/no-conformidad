<?php

use Illuminate\Database\Seeder;

use App\Departamento;
use App\Tipo;
use App\Status;
use App\User;
use App\Role;

use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
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
            'nombre' => 'Empresa 2, Barco II'
        ]);

        /* Creadores */
        $user = User::create([
            'name'=>'Creador1',
            'email'=>'creador1@localhost',
            'password'=>Hash::make('nocon'),
            'serie_documentos'=>'MX1'
        ]);
        $user->addRole('creador');
        $user->departamentos()->attach($departamento1->id);
        $user = User::create([
            'name'=>'Creador2',
            'email'=>'creador2@localhost',
            'password'=>Hash::make('nocon'),
            'serie_documentos'=>'ABC'
        ]);
        $user->addRole('creador');
        $user->departamentos()->attach($departamento2->id);

        /* Directores */
        $user = User::create([
            'name'=>'Director1',
            'email'=>'director1@localhost',
            'password'=>Hash::make('nocon'),
        ]);
        $user->addRole('director');
        $user->departamentos()->attach($departamento1->id);
        $user = User::create([
            'name'=>'Director2',
            'email'=>'director2@localhost',
            'password'=>Hash::make('nocon'),
        ]);
        $user->addRole('director');
        $user->departamentos()->attach($departamento2->id);

        /* Responsables */
        $user = User::create([
            'name'=>'Responsable1',
            'email'=>'responsable1@localhost',
            'password'=>Hash::make('nocon'),
          ]);
        $user->addRole('responsable');
        $user->departamentos()->attach($departamento1->id);

        $user = User::create([
            'name'=>'Responsable2',
            'email'=>'responsable2@localhost',
            'password'=>Hash::make('nocon'),
          ]);
        $user->addRole('responsable');
        $user->departamentos()->attach($departamento2->id);
    }
}
