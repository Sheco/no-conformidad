<?php
$personas = [
    $barco = App\User::find(1),
    $ism = App\User::find(2),
    $responsable = App\User::find(3),
];

DB::table('propuestas')->truncate();
DB::table('documentos')->truncate();

$tipo = App\Tipo::find(1);

/* paso 1, nuevo documento */
$doc = App\Documento::nuevo($barco, $tipo, 'prueba');
//$doc = App\Documento::find(5);

/* paso 2, se asigna responsable */ 
$doc->asignarResponsable($responsable);
$doc->save();

/* paso 3, el responsable agrega una propuesta */
$doc->agregarPropuesta($responsable, 'propuesta');
$doc->save();

/* paso 4, rechazar y reasignar */
$propuesta = $doc->propuestas->last();
$rechazar = true;
if($rechazar) {
    $doc->rechazarPropuesta($propuesta, $ism, 'no me gusta');
    $doc->asignarResponsable($responsable);
    $propuesta->save();
    $doc->save();
    exit();
}


/* paso 5, aceptar la propuesta */
$doc->aceptarPropuesta($propuesta, $ism, 'perfecto');
$propuesta->save();
$doc->save();

/* paso 6, marcar como completado */
$doc->corregido($responsable);
$doc->save();

/* paso 7, marcar como verificado */
$doc->verificado($barco);
$doc->save();

/* paso 9, cerrar el documento */
$doc->cerrar($barco);
$doc->save();
