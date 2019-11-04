<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Status;
use App\Documento;
use App\User;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status='inicio')
    {
        $user  = Auth::user();
        $statuses = Status::all();
        $docs = Documento::visible($user)
            ->status($status)
            ->get();

        return view("documentos.index", compact('status', 'statuses', 'docs', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ver(Documento $documento)
    {
        $responsables = User::whereHas('roles', function($q) {
            $q->where('name', 'responsable');
        })->get()->pluck('name', 'id')->toArray();

        $puedeAsignarResponsable = Gate::allows('asignarResponsable', $documento);

        return view('documentos.ver', compact('documento', 'responsables',
            'puedeAsignarResponsable'));
    }

    public function asignarResponsable(Request $request) {
        $documento = Documento::findOrFail($request->input('documento_id'));
        Gate::authorize('asignarResponsable', $documento);
        $responsable = User::findOrFail($request->input('responsable_usr_id'));

        $documento->asignarResponsable(Auth::user(), $responsable);
        $documento->save();
        return "Guardado";
    }
}
