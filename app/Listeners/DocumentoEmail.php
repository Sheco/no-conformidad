<?php

namespace App\Listeners;

use App\Events\DocumentoActualizado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(DocumentoActualizado $event)
    {
        //
    }
}
