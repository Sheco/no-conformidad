<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use NoconTableSeeder;
use App\User;
use App\Documento;
use App\Tipo;
use App\Departamento;
use Carbon\Carbon;

class FlujoTest extends TestCase
{
    public function testFlujo() {
        $this->artisan("migrate:fresh");
        $this->seed(NoconTableSeeder::class);
        $this->assertDatabaseHas('users', [
          'email'=>'creador1@nocon.com'
        ]);

        $tipo = Tipo::find(1);
        $departamento = Departamento::find(1);

        $creador = User::where('email', 'creador1@nocon.com')->first();
        $director = User::where('email', 'director1@nocon.com')->first();
        $responsable = User::where('email', 'responsable1@nocon.com')->first();

        $doc = new Documento;
        $doc->crear($creador, $tipo, $departamento, 'huecote', 'hay un hueco');

        $rechazos = 2;
        do {
            /* paso 2, se asigna responsable */ 
            $doc->asignarResponsable($director, $responsable);

            /* paso 3, el responsable agrega una propuesta */
            $texto = ($rechazos>0? 'no hacer nada': 'tapar el hueco');
            $fecha = Carbon::now()->addDays(30)->format('Y-m-d');
            $propuesta = $doc->agregarPropuesta($responsable, $texto, $fecha);
            $doc->refresh();

            /* paso 4, rechazar y reasignar */
            if($rechazos>0) {
                $doc->rechazarPropuesta($director, $propuesta, 'mala idea');
                $rechazos--;
            } else break;
        } while(true);

        /* paso 5, aceptar la propuesta */
        $doc->aceptarPropuesta($director, $propuesta, 'perfecto');

        /* paso 6, marcar como completado */
        $doc->corregir($responsable);

        /* paso 7, marcar como verificado */
        $doc->verificar($creador);

        /* paso 9, cerrar el documento */
        $doc->cerrar($creador);
    }

    public function testSetup() {
        $this->artisan('migrate:fresh');
        $this->seed(NoconTableSeeder::class);
        $this->assertDatabaseHas('users', [
          'email'=>'creador1@nocon.com'
        ]);

    } 

    public function testDirectorNoPuedeCrear() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $director = User::where('email', 'director1@nocon.com')->first();

        $doc = new Documento;
        
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $doc->crear($director, $tipo, $departamento1, 'titulo', 'descripcion');
    }

    public function testResponsableNoPuedeCrear() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $responsable = User::where('email', 'responsable1@nocon.com')->first();

        $doc = new Documento;
        
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $doc->crear($responsable, $tipo, $departamento1, 'titulo', 'descripcion');
    }

    public function testCreadorNoPuedeAsignarResponsable() {
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $creador = User::where('email', 'creador1@nocon.com')->first();
        $responsable = User::where('email', 'creador1@nocon.com')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento1, 'titulo', 'descripcion');
        $doc->asignarResponsable($creador, $responsable); 
    }

    public function testResponsableNoPuedeAsignarResponsable() {
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $creador = User::where('email', 'creador1@nocon.com')->first();
        $responsable = User::where('email', 'creador1@nocon.com')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento1, 'titulo', 'descripcion');
        $doc->asignarResponsable($responsable, $responsable); 
    }
}
