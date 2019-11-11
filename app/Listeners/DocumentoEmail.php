<?php

namespace App\Listeners;

use App\Events\DocumentoActualizado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class DocumentoEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DocumentoActualizado  $event
     * @return void
     */
    public function handle(DocumentoActualizado $evento)
    {
      /*
       * crear, mandar al creador y todos los directores que puedan ver el doc
       * asignarResponsable, mandar al responsable
       * agregarPropuesta, mandar a los directores
       * aceptar/rechazarPropuesta, mandar al responsable
       * corregir, mandar al creador
       */

      $recipients = [
        "crear"=>function($doc) { 
          return $doc->directores(); 
        },
        "asignarResponsable"=>function($doc) { 
          return $doc->responsable; 
        },
        "agregarPropuesta"=>function($doc) { 
          return $doc->directores(); 
        },
        "aceptarPropuesta"=>function($doc) {
          return $doc->responsable;
        },
        "rechazarPropuesta"=>function($doc) {
          return $doc->responsable;
        },
        "corregir"=>function($doc) {
          return $doc->creador;
        }
      ];

      $metodo = $evento->metodo;
      $documento = $evento->documento;
      $propuesta = $evento->propuesta;

      if(!isset($recipients[$metodo]))
        return;

      $to = $recipients[$metodo]($documento);
      $mail = new \App\Mail\DocumentoActualizado($documento, 
        $propuesta, $metodo);

      Mail::to($to)->queue($mail);
    }
}
