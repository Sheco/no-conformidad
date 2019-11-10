<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view("admin/users/index", compact('users'));
    }

    public function logs(User $user) {
      $logs = $user->logs()->orderBy('fecha', 'desc')->get();
      return view('admin/users/logs', compact('user', 'logs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User;
        $departamentos = Departamento::all();
        $roles = Role::all();
        return view("admin/users/create", compact(
            'user', 'departamentos', 'roles'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
        ]);

        $input = $request->input();
        if(!empty($input['password']))
            $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        return redirect(action("Admin\UsersController@edit", [$user->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $departamentos = Departamento::orderBy('nombre')
            ->get();
        $departamentosDisponibles = Departamento::whereNotIn('id',$user->departamentos->pluck('id'))
            ->orderBy('nombre')
            ->get();
        $roles = Role::whereNotIn('id', $user->roles->pluck('id'))->get();
        return view("admin.users.edit", compact(
            'user', 'departamentos', 'roles',
            'departamentosDisponibles'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return redirect(action("Admin\UsersController@index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->departamentos()->detach();
        $user->roles()->detach();
        $user->delete();
        return redirect(action("Admin\UsersController@index"));
    }

    public function addDepartamento(Request $request, User $user) {
        $id = $request->input('departamento_id');
        if($user->departamentos->where('id', $id)->count())
            return back();

        $user->departamentos()->attach($id);
        return back();
    }

    public function delDepartamento(User $user, Departamento $departamento) {
        $user->departamentos()->detach($departamento->id);
        return back();
        
    }

    public function addRole(Request $request, User $user) {
        $id = $request->input('role_id');
        if($user->roles->where('id', $id)->count())
            return back();

        $user->roles()->attach($request->input('role_id'));
        return back();

    }

    public function delRole(User $user, Role $role) {
        $user->roles()->detach($role->id);
        return back();
    }
}
