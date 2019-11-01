<?php

$personas = [
    $barco = App\User::find(1),
    $ism = App\User::find(2),
    $responsable = App\User::find(3),
];

$tipo = App\Tipo::find(1);

foreach($personas as $persona) {
    echo "Documentos relevantes para: $persona->name\n";
    $docs = App\Documento::visible($persona)->get();
    foreach($docs as $doc) {
        echo "- documento: $doc->id, folio: $doc->folio, {$doc->status->nombre}, $doc->descripcion\n";
        echo " creador: {$doc->creador->name}, responsable: ". ($doc->responsable? $doc->responsable->name: '') ."\n";
        foreach($doc->propuestas as $prop) {
            echo "-- propuesta: $prop->id, {$prop->responsable->name}, $prop->descripcion\n";
        }
    }
    echo "\n";
}
exit;
