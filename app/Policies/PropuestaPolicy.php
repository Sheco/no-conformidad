<?php

namespace App\Policies;

use App\User;
use App\Propuesta;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class PropuestaPolicy
{
    use HandlesAuthorization;

    public function rechazar(User $user, Propuesta $propuesta) {
      $doc = $propuesta->documento;
      if(!$user->hasRole('director')) 
          return Response::deny("El usuario $user->name no puede rechazar propuestas, no tiene el rol apropiado.");

      if($doc->status->codigo != 'pendiente-revision')
          return Response::deny('Solo se puede rechazar propuestas cuando estan pendientes de revisión');
      
      if($propuesta->documento->propuestas->last()->id != $propuesta->id)
        return Response::deny("Solo se puede rechazar la ultima propuesta del documento, ");

      return Response::allow();
    }

    public function aceptar(User $user, Propuesta $propuesta) {
      $doc = $propuesta->documento;

      if(!$user->hasRole('director'))
          return Response::deny("Solo los directores pueden aceptar propuestas.");

      if($doc->status->codigo != 'pendiente-revision')
          return Response::deny('Solo se puede aceptar propuestas cuando estan pendientes de revisión');
      
      if($propuesta->documento->propuestas->last()->id != $propuesta->id)
        return Response::deny("Solo se puede aceptar la ultima propuesta del documento, ");

      return Response::allow();
    } 
}
