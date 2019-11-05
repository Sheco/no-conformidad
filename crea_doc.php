<?php
use Carbon\Carbon;

// cuantas veces rechazar
$totalRechazos = 2;
$personas = [
    $creador = App\User::where('email', 'creador1@nocon.com')->first(),
    $director = App\User::where('email', 'director1@nocon.com')->first(),
    $responsable = App\User::where('email', 'responsable1@nocon.com')->first(),
];
function espera() {
    sleep(5);
}

DB::table('propuestas')->truncate();
DB::table('documentos')->truncate();

$tipo = App\Tipo::find(1);
$departamento = App\Departamento::find(1);

/* paso 1, nuevo documento */
echo "Creando nuevo documento...\n";
$doc = new App\Documento;
$doc->crear($creador, $tipo, $departamento, 'huecote', 'hay un hueco');
//$doc = App\Documento::find(5);
espera();

$rechazos = 0;
do {
    /* paso 2, se asigna responsable */ 
    echo "Asignando responsable...\n";
    $doc->asignarResponsable($director, $responsable);
    espera();

    /* paso 3, el responsable agrega una propuesta */
    echo "Agregando propuesta...\n";
    $texto = ($rechazos<$totalRechazos? 'no hacer nada': 'tapar el hueco');
    $fecha = Carbon::now()->addDays(30)->format('Y-m-d');
    $propuesta = $doc->agregarPropuesta($responsable, $texto, $fecha);
    $doc->refresh();
    espera();

    /* paso 4, rechazar y reasignar */
    if($rechazos<$totalRechazos) {
        echo "Rechazando propuesta...\n";
        $doc->rechazarPropuesta($director, $propuesta, 'mala idea');
        espera();
        $rechazos++;
    } else break;
} while(true);

/* paso 5, aceptar la propuesta */
echo "Aceptando propuesta...\n";
$doc->aceptarPropuesta($director, $propuesta, 'perfecto');
espera();

/* paso 6, marcar como completado */
echo "Marcando el documento como corregido...\n";
$doc->corregir($responsable);
espera();

/* paso 7, marcar como verificado */
echo "Marcando el documento como verificado...\n";
$doc->verificar($creador);
espera();

/* paso 9, cerrar el documento */
echo "Marcando el documento como cerrado...\n";
$doc->cerrar($creador);
espera();
