<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Documento;
use App\Propuesta;

class DocumentoActualizado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Documento $documento, ?Propuesta $propuesta, $metodo)
    {
      $this->documento = $documento;
      $this->metodo = $metodo;
      $this->propuesta = $propuesta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->view("documento_mails.$this->metodo", [
        'documento'=>$this->documento,
        'propuesta'=>$this->propuesta
      ]);
    }
}
