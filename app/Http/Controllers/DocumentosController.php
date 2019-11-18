<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Status;
use App\Documento;
use App\DocumentoArchivo;
use App\Propuesta;
use App\Tipo;
use App\Departamento;
use App\User;
use Arr;

use Carbon\Carbon;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status='')
    {
        $user  = Auth::user();
        $statuses = collect([Status::wildcard()])->concat(Status::all());
        $docs = Documento::with('creador')
            ->with('responsable')
            ->with('tipo')
            ->with('status')
            ->visible($user)
        ;

        if($status)
            $docs = $docs->status($status);

        $filtros = session("filtros");
        if(Arr::get($filtros, 'creador_id', null)) {
            $docs = $docs->where('creador_id', 
                $filtros['creador_id']);
        }
        if(Arr::get($filtros, 'departamento_id', null)) {
            $docs = $docs->where('departamento_id', 
                $filtros['departamento_id']);
        }
        if(Arr::get($filtros, 'tipo_id', null)) {
            $docs = $docs->where('tipo_id', 
                $filtros['tipo_id']);
        }

        $docs = $docs
            ->orderBy('limite_actual', 'asc')
            ->cursor();

        return view("documentos.index", compact('status', 'statuses', 'docs', 'user'));
    }

    function filtros() {
        $filtros = session('filtros', []);
        $user = Auth::user();
        $departamentos = $user->departamentos->pluck('id');
        $usuarios = User::whereHas("departamentos", 
            function($q) use ($departamentos) {
                $q->whereIn("id", $departamentos);
            })->get()
              ->pluck('name', 'id')
              ->prepend('-- Cualquiera', '');

        $departamentos = $user->departamentos()
            ->get()
            ->pluck('nombre', 'id')
            ->prepend('-- Cualquiera', '');

        $tipos = Tipo::get()
            ->pluck('nombre', 'id')
            ->prepend('-- Cualquiera', '');

        return view("documentos.filtros", compact(
            'filtros', 
            'usuarios',
            'departamentos',
            'tipos',
        ));
    }

    function filtrosGuardar(Request $request) {
        $user = Auth::user();

        $filtrosValidos = ['departamento_id', 'tipo_id'];
        if($user->hasRole('admin') or $user->hasRole('director')) 
            $filtrosValidos[] = 'creador_id';

        $filtros = $request->only($filtrosValidos);
        session(["filtros"=>$filtros]);

        return redirect("/docs");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        $documento = new Documento;
        $tipos = Tipo::all()->pluck('nombre', 'id');
        $user = Auth::user();
        $departamentos = $user->departamentos->pluck('nombre', 'id');

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
        $request->validate([
            "titulo"=>"required",
            "descripcion"=>"required",
            "departamento_id"=>'required',
        ]);

        $doc = new Documento;
        $doc->crear(Auth::user(), 
            Tipo::findOrFail($request->input('tipo_id')), 
            Departamento::findOrFail($request->input('departamento_id')),
            $request->input('titulo'),  
            $request->input('descripcion'),
            $request->file('archivo')
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
        $user = Auth::user();
        $responsables = User::whereHas('roles', function($q) {
            $q->where('name', 'responsable');
        })->whereHas('departamentos', function($q) use ($documento) {
            $q->where('id', $documento->departamento_id);
        })
          ->get()
          ->pluck('name', 'id')->toArray();

        return view('documentos.ver', compact(
            'documento', 
            'responsables',
        ));
    }

    public function archivo(DocumentoArchivo $archivo) {
        return Storage::download("documentos/$archivo->id", $archivo->nombre);
    }

    public function asignarResponsable(Request $request, Documento $documento) {
        $responsable = User::find($request->input('responsable_usr_id'));

        $documento->asignarResponsable(Auth::user(), $responsable);
        return back();
    }

    public function agregarPropuesta(Request $request, Documento $documento) {
        $request->validate([
            'fecha_entrega'=>'required',
            'descripcion'=>'required'
        ]);
        $documento->agregarPropuesta(Auth::user(), $request->input('descripcion'), $request->input('fecha_entrega'));
        return back();
    }

    public function rechazarPropuesta(Request $request, Propuesta $propuesta) {
        $documento = $propuesta->documento;
        $documento->rechazarPropuesta(Auth::user(), $propuesta, '');
        return back();
    }

    public function aceptarPropuesta(Request $request, Propuesta $propuesta) {
        $documento = $propuesta->documento;
        $documento->aceptarPropuesta(Auth::user(), $propuesta, '');
        return back();
    }

    public function corregir(Request $request, Documento $documento) {
        $documento->corregir(Auth::user());
        return back();
    }

    public function verificar(Request $request, Documento $documento) {
        $documento->verificar(Auth::user());
        return back();
    }

    public function cerrar(Request $request, Documento $documento) {
        $documento->cerrar(Auth::user());
        return back();
    }

    public function logs(Documento $documento) {
        Gate::authorize('ver', $documento);
        $logs = $documento->logs()->orderBy('fecha', 'desc')->cursor();
        return view("documentos.logs", compact('documento', 'logs'));
    }
}
