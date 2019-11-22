<?php

namespace App\Policies;

use App\Documento;
use App\User;
use App\Departamento;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Carbon\Carbon;

class DocumentoPolicy
{
    use HandlesAuthorization;

    public function crearDocumentos(User $user) {
        if(!$user->hasRole('creador'))
            return Response::deny("El usuario $user->name no puede crear documentos, no tiene el rol apropiado.");

        return Response::allow();
    }

    public function crear(User $user, Documento $doc, Departamento $departamento) {
        $respuesta = $this->crearDocumentos($user, $doc);
        if(!$respuesta->allowed())
            return $respuesta;

        if($departamento && !$user->departamentos()->where('id', $departamento->id)->exists()) {
            return Response::deny("El usuario $user->name no esta suscrito al departamento $departamento->nombre");
        }

        return Response::allow();
    }

    public function asignarResponsables(User $user, Documento $doc) {
        if(!in_array($doc->status->codigo, ['inicio', 'pendiente-propuesta']))
            return Response::deny('Para asignar un responsable, el documento tiene que esta al inicio de su proceso o estar pendiente de una propuesta.');

        if(!$user->hasRole('director'))
            return Response::deny("El usuario $user->name no puede asignar responsables, no tiene el rol apropiado.");

        return Response::allow();
    }

    public function asignarResponsable(User $user, Documento $doc, User $responsable) {
        $respuesta = $this->asignarResponsables($user, $doc);

        if(!$respuesta->allowed())
            return $respuesta;

        if(!$responsable->hasRole('responsable'))
            return Response::deny("El usuario $responsable->name no puede encargarse de este documento, no tiene el rol apropiado.");

        if(!$responsable->departamentos
            ->where('id', $doc->departamento_id) 
            ->count()) {
            return Response::deny("El usuario $responsable->name no puede encargarse de este documento, no tiene el departamento {$doc->departamento->nombre}");
        }

        return Response::allow();
    }

    public function agregarPropuestas(User $user, Documento $doc) {
        if(!$doc->responsable_usr_id)
            return Response::deny('En este momento nadie puede agregar propuestas a este documento.');
        else if($user->id != $doc->responsable_usr_id)
            return Response::deny("Solo {$doc->responsable->name} puede agregar propuestas a este documento.");

        if($doc->status->codigo != 'pendiente-propuesta') 
            return Response::deny('Solo se puede agregar una propuesta a aquellos documentos que esten esperando una propuesta');

        if(!$user->hasRole('responsable'))
            return Response::deny("El usuario $user->name no puede agregar propuestas, no tiene el rol apropiado.");

        return Response::allow();
    }

    public function agregarPropuesta(User $user, Documento $doc, Carbon $fecha_entrega) {
        $respuesta = $this->agregarPropuestas($user, $doc);
        if(!$respuesta->allowed())
            return $respuesta;

        if($fecha_entrega > $doc->limiteMaximoPropuesta)
            return Response::deny("La fecha propuesta de entrega no puede exceder la fecha maxima de $doc->limiteMaximoPropuesta");

        return Response::allow();
    }

    public function corregir(User $user, Documento $doc) {
        if($user->id != $doc->responsable_usr_id)
            return Response::deny("Solo el responsable pude marcar el documento como corregido");

        if($doc->status->codigo != 'en-progreso')
            return Response::deny("Solo se pueden marcar como corregidos aquellos documentos que esten en progreso.");

        if(!$user->hasRole('responsable'))
            return Response::deny("El usuario $user->name no puede marcar el documento como corregido, no tiene el rol apropiado");

        return Response::allow();
    }

    public function verificar(User $user, Documento $doc) {
        if($user->id != $doc->creador_usr_id) 
            return Response::deny("Solo {$doc->creador->name} puede marcar este documento como verificado");

        if($doc->status->codigo != 'corregido')
            return Response::deny("Solo se pueden marcar como verificado aquellos documentos que esten marcados como corregidos.");

        return Response::allow();
    }

    public function cerrar(User $user, Documento $doc) {
        if($user->id != $doc->creador_usr_id) 
            return Response::deny("Solo {$doc->creador->name} puede cerrar este documento.");

        if($doc->status->codigo != 'verificado')
            return Response::deny("Solo se pueden cerrar aquellos documentos que esten marcados como verificados.");

        return Response::allow();
    }

    public function ver(User $user, Documento $doc) {
        $visible = Documento::visible($user)
            ->where('id', $doc->id)
            ->exists();

        if(!$visible) 
            return Response::deny("El usuario {$user->name} no puede ver el documento {$doc->folio}");

        return Response::allow();
    }

}
