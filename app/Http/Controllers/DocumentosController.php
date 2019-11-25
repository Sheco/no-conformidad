<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function index(Request $request, $status='')
    {
        $user  = $request->user();
        $statuses = collect([Status::wildcard()])->concat(Status::all());
        $filtros = session("filtros", []);
        $docs = Documento::with('creador')
            ->with('responsable')
            ->with('tipo')
            ->with('status')
            ->with('departamento')
            ->visible($user)
            ->filtrados($filtros)
            ->status($status)
            ->orderBy('limite_actual', 'asc')
            ->get();

        $ui_filtros = collect($filtros)->mapWithKeys(function($value, $key) {
            if($key == 'creador_usr_id') {
                return [ 'Creador' => User::find($value)->name ];
            }
            if($key == 'departamento_id') {
                return [ 'Departamento' => Departamento::find($value)->nombre ];
            }
            if($key == 'tipo_id') {
                return [ 'Tipo' => Tipo::find($value)->nombre ];
            }
        });

        return view("documentos.index", compact(
            'status', 
            'statuses', 
            'docs', 
            'user',
            'filtros',
            'ui_filtros',
        ));
    }

    function filtros(Request $request) {
        $filtros = session('filtros', []);
        $user = $request->user();
        $departamentos = $user->departamentos->pluck('id');
        $usuarios = User::whereHas("departamentos", 
            function($q) use ($departamentos) {
                $q->whereIn("id", $departamentos);
            })->cursor()
              ->pluck('name', 'id');

        $departamentos = $user->departamentos()
            ->cursor()
            ->pluck('nombre', 'id');

        $tipos = Tipo::cursor()
            ->pluck('nombre', 'id');

        return view("documentos.filtros", compact(
            'filtros', 
            'usuarios',
            'departamentos',
            'tipos',
        ));
    }

    function filtrosGuardar(Request $request) {
        $user = $request->user();

        $filtrosValidos = ['departamento_id', 'tipo_id'];
        if($user->hasRole('admin') or $user->hasRole('director')) 
            $filtrosValidos[] = 'creador_usr_id';

        $filtros = collect($request->only($filtrosValidos))
            ->filter(function($x) {
                return $x != "";
            })->toArray();
        session(["filtros"=>$filtros]);

        return redirect($request->input('redirect'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {
        $documento = new Documento;
        $tipos = Tipo::all()->pluck('nombre', 'id');
        $user = $request->user();
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
            'tipo_id'=>'required',
        ]);

        $doc = new Documento;
        $doc->crear($request->user(), 
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
    public function ver(Request $request, Documento $documento)
    {
        $user = $request->user();
        $responsables = User::whereHas('roles', function($q) {
            $q->where('name', 'responsable');
        })->whereHas('departamentos', function($q) use ($documento) {
            $q->where('id', $documento->departamento_id);
        })
          ->cursor()
          ->pluck('name', 'id');

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

        $documento->asignarResponsable($request->user(), $responsable);
        return back();
    }

    public function agregarPropuesta(Request $request, Documento $documento) {
        $request->validate([
            'fecha_entrega'=>'required',
            'descripcion'=>'required'
        ]);
        $documento->agregarPropuesta($request->user(), $request->input('descripcion'), $request->input('fecha_entrega'));
        return back();
    }

    public function rechazarPropuesta(Request $request, Propuesta $propuesta) {
        $propuesta->rechazar($request->user(), '');
        return back();
    }

    public function aceptarPropuesta(Request $request, Propuesta $propuesta) {
        $propuesta->aceptar($request->user(), '');
        return back();
    }

    public function corregir(Request $request, Documento $documento) {
        $documento->corregir($request->user());
        return back();
    }

    public function verificar(Request $request, Documento $documento) {
        $documento->verificar($request->user());
        return back();
    }

    public function cerrar(Request $request, Documento $documento) {
        $documento->cerrar($request->user());
        return back();
    }

    public function logs(Documento $documento) {
        $logs = $documento->logs()->orderBy('fecha', 'desc')->cursor();
        return view("documentos.logs", compact('documento', 'logs'));
    }
}
