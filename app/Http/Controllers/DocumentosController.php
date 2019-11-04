<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Status;
use App\Documento;
use App\Tipo;
use App\Departamento;
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
    public function crear()
    {
        Gate::authorize('crear', Documento::class);
        $documento = new Documento;
        $tipos = Tipo::all()->pluck('nombre', 'id');
        $departamentos = Departamento::all()->pluck('nombre', 'id');
        return view('documentos.crear', compact('documento', 'tipos', 'departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        Gate::authorize('crear', Documento::class);
        $tipo = Tipo::findOrFail($request->input('tipo_id'));
        $departamento = Departamento::findOrFail($request->input('departamento_id'));

        $doc = new Documento;
        $doc->crear(Auth::user(), $tipo, $departamento,
            $request->input('titulo'),  
            $request->input('descripcion')
        );
        return redirect('/docs');
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
        $responsable = User::find($request->input('responsable_usr_id'));

        $documento->asignarResponsable(Auth::user(), $responsable);
        $documento->save();
        return "Guardado";
    }
}
