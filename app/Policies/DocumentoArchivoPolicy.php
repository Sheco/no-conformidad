<?php

namespace App\Policies;

use App\DocumentoArchivo;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class DocumentoArchivoPolicy
{
    use HandlesAuthorization;

    function ver(User $user, DocumentoArchivo $archivo) {
      $response = Gate::inspect('ver', $archivo->documento);
      if($response->allowed())
        return Response::allow();

      return Response::deny($response->message());
    }
}
