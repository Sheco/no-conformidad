<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;
use TestSeeder;
use BaseSeeder;
use App\User;
use App\Documento;
use App\Tipo;
use App\Departamento;
use Carbon\Carbon;

class FlujoTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp():void {
        parent::setUp();
        $this->seed(BaseSeeder::class);
        $this->seed(TestSeeder::class);
    }

    public function testFlujo() {
        $this->assertDatabaseHas('users', [
          'email'=>'creador1@localhost'
        ]);

        $tipo = Tipo::find(1);
        $departamento = Departamento::find(1);

        $creador = User::where('email', 'creador1@localhost')->first();
        $director = User::where('email', 'director1@localhost')->first();
        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        $doc->crear($creador, $tipo, $departamento, 'huecote', 'hay un hueco');
        $this->assertDatabaseHas('documentos', ['id'=>$doc->id]);

        /* paso 2, se asigna responsable */ 
        $doc->asignarResponsable($director, $responsable);
        $this->assertTrue($doc->responsable_usr_id == $responsable->id);

        $rechazos = 2;
        do {
            /* paso 3, el responsable agrega una propuesta */
            $texto = ($rechazos>0? 'no hacer nada': 'tapar el hueco');
            $fecha = Carbon::now()->addDays(30)->format('Y-m-d');
            $propuesta = $doc->agregarPropuesta($responsable, $texto, $fecha);
            $doc->refresh();
            $this->assertTrue($doc->propuestas->last()->id == $propuesta->id);

            /* paso 4, rechazar */
            if($rechazos>0) {
                $propuesta->rechazar($director, 'mala idea');
                $doc->refresh();
                $this->assertTrue($doc->propuestas->last()->status == false);
                $rechazos--;
            } else {
                /* paso 5, aceptar la propuesta */
                $propuesta->aceptar($director, 'perfecto');
                $doc->refresh();
                $this->assertTrue($doc->propuestas->last()->status == true);
                break;
            }
        } while(true);

        /* paso 6, marcar como completado */
        $doc->corregir($responsable);
        $doc->refresh();
        $this->assertTrue($doc->status->codigo=='corregido');

        /* paso 7, marcar como verificado */
        $doc->verificar($creador);
        $doc->refresh();
        $this->assertTrue($doc->status->codigo=='verificado');

        /* paso 9, cerrar el documento */
        $doc->cerrar($creador);
        $doc->refresh();
        $this->assertTrue($doc->status->codigo=='cerrado');
    }

    public function testDirectorNoPuedeCrear() {
        $tipo = Tipo::where(['id'=>1])->first();
        $departamento1 = Departamento::find(1);

        $director = User::where('email', 'director1@localhost')->first();

        $doc = new Documento;
        
        $this->expectException(AuthorizationException::class);
        $doc->crear($director, $tipo, $departamento1, 'titulo', 'descripcion');
    }

    public function testResponsableNoPuedeCrear() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        
        $this->expectException(AuthorizationException::class);
        $doc->crear($responsable, $tipo, $departamento1, 'titulo', 'descripcion');
    }

    public function testCreadorNoPuedeAsignarResponsable() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $creador = User::where('email', 'creador1@localhost')->first();
        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento1, 'titulo', 'descripcion');
        $this->expectException(AuthorizationException::class);
        $doc->asignarResponsable($creador, $responsable); 
    }

    public function testResponsableNoPuedeAsignarResponsable() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $creador = User::where('email', 'creador1@localhost')->first();
        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento1, 'titulo', 'descripcion');
        $this->expectException(AuthorizationException::class);
        $doc->asignarResponsable($responsable, $responsable); 
    }

    public function testCreadorNoPuedeCrearEnOtroDepartamento() {
        $tipo = Tipo::find(1);
        $departamento = new Departamento;
        $departamento->nombre = "test";
        $departamento->save();
        $creador = User::where('email', 'creador1@localhost')->first();

        $doc = new Documento;
        $this->expectException(AuthorizationException::class);
        $doc->crear($creador, $tipo, $departamento, 'titulo', 'descripcion');
    }

    public function testDirectorNoPuedeAsignarResponsableConStatusIncorrecto() {
        $tipo = Tipo::find(1);
        $departamento1 = Departamento::find(1);

        $creador = User::where('email', 'creador1@localhost')->first();
        $director = User::where('email', 'director1@localhost')->first();
        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento1, 'titulo', 'descripcion');
        $doc->asignarResponsable($director, $responsable); 
        $fecha = Carbon::now()->addDays(30)->format('Y-m-d');
        $propuesta = $doc->agregarPropuesta($responsable, "test", $fecha);

        $this->expectException(AuthorizationException::class);
        $doc->asignarResponsable($director, $responsable); 
    }

    public function testDirectorNoPuedeAsignarResponsableDeOtroDepto() {
        $tipo = Tipo::find(1);
        $departamento = new Departamento;
        $departamento->nombre = "test";
        $departamento->save();

        $creador = User::where('email', 'creador1@localhost')->first();
        $creador->departamentos()->attach($departamento);

        $director = User::where('email', 'director1@localhost')->first();
        $director->departamentos()->attach($departamento);
        $responsable = User::where('email', 'responsable1@localhost')->first();

        $doc = new Documento;
        
        $doc->crear($creador, $tipo, $departamento, 'titulo', 'descripcion');
        $this->expectException(AuthorizationException::class);
        $doc->asignarResponsable($director, $responsable); 
    }
}
