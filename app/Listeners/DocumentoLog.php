<?php

namespace App\Listeners;

use App\Events\DocumentoActualizado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DocumentoLog
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
      $mensaje = view("documento_logs.$evento->metodo", compact('evento'))->render();
      DB::table('documento_logs')->insert([
        'user_id'=>$evento->user->id,
        'documento_id'=>$evento->documento->id,
        'mensaje'=>trim($mensaje),
        'fecha'=>strftime('%F %T'),
      ]);
    }

}
