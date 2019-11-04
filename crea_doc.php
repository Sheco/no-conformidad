<?php

use Illuminate\Support\Facades\Gate;

// cuantas veces rechazar
$totalRechazos = 2;
$personas = [
    $barco = App\User::find(1),
    $ism = App\User::find(2),
    $responsable = App\User::find(3),
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
Gate::forUser($barco)->authorize('crear', App\Documento::class);
$doc->crear($barco, $tipo, $departamento, 'huecote', 'hay un hueco');
//$doc = App\Documento::find(5);
espera();

$rechazos = 0;
do {
    /* paso 2, se asigna responsable */ 
    echo "Asignando responsable...\n";
    Gate::forUser($ism)->authorize('asignarResponsable', $doc);
    $doc->asignarResponsable($ism, $responsable);
    $doc->save();
    espera();

    /* paso 3, el responsable agrega una propuesta */
    echo "Agregando propuesta...\n";
    $texto = ($rechazos<$totalRechazos? 'no hacer nada': 'tapar el hueco');
    Gate::forUser($responsable)->authorize('agregarPropuesta', $doc);
    $propuesta = $doc->agregarPropuesta($responsable, $texto);
    $doc->save();
    espera();

    /* paso 4, rechazar y reasignar */
    if($rechazos<$totalRechazos) {
        echo "Rechazando propuesta...\n";
        Gate::forUser($ism)->authorize('rechazarPropuesta', $doc);
        $doc->rechazarPropuesta($ism, $propuesta, 'mala idea');
        $propuesta->save();
        $doc->save();
        espera();
        $rechazos++;
    } else break;
} while(true);

/* paso 5, aceptar la propuesta */
echo "Aceptando propuesta...\n";
Gate::forUser($ism)->authorize('aceptarPropuesta', $doc);
$doc->aceptarPropuesta($ism, $propuesta, 'perfecto');
$propuesta->save();
$doc->save();
espera();

/* paso 6, marcar como completado */
echo "Marcando el documento como corregido...\n";
Gate::forUser($responsable)->authorize('corregir', $doc);
$doc->corregir($responsable);
$doc->save();
espera();

/* paso 7, marcar como verificado */
echo "Marcando el documento como verificado...\n";
Gate::forUser($barco)->authorize('verificar', $doc);
$doc->verificar($barco);
$doc->save();
espera();

/* paso 9, cerrar el documento */
echo "Marcando el documento como cerrado...\n";
Gate::forUser($barco)->authorize('cerrar', $doc);
$doc->cerrar($barco);
$doc->save();
espera();
