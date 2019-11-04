<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Status;
use App\Documento;
use App\Propuesta;
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
        $ultimaPropuesta = $documento->propuestas->count()
            ? $documento->propuestas->last()->id
            : 0;

        return view('documentos.ver', compact('documento', 'responsables',
            'puedeAsignarResponsable', 'ultimaPropuesta'));
    }

    public function asignarResponsable(Request $request, Documento $documento) {
        $responsable = User::find($request->input('responsable_usr_id'));

        $documento->asignarResponsable(Auth::user(), $responsable);
        $documento->save();
        return back();
    }

    public function agregarPropuesta(Request $request, Documento $documento) {
        $documento->agregarPropuesta(Auth::user(), $request->input('descripcion'));
        $documento->save();
        return back();
    }

    public function rechazarPropuesta(Request $request, Propuesta $propuesta) {
        $documento = $propuesta->documento;
        $documento->rechazarPropuesta(Auth::user(), $propuesta, '');
        $propuesta->save();
        $documento->save();
        return back();
    }

    public function aceptarPropuesta(Request $request, Propuesta $propuesta) {
        $documento = $propuesta->documento;
        $documento->aceptarPropuesta(Auth::user(), $propuesta, '');
        $propuesta->save();
        $documento->save();
        return back();
    }

    public function corregir(Request $request, Documento $documento) {
        $documento->corregir(Auth::user());
        $documento->save();
        return back();
    }

    public function verificar(Request $request, Documento $documento) {
        $documento->verificar(Auth::user());
        $documento->save();
        return back();
    }

    public function cerrar(Request $request, Documento $documento) {
        $documento->cerrar(Auth::user());
        $documento->save();
        return back();
    }
}
