<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        $usuarios = User::all();
        return $usuarios;
    }

    public function show($id)
    {
        $usuarios = User::find($id);
        return $usuarios;
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->name = $request->nombre." ".$request->apellidos;
        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellidos;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->direccion = $request->direccion;
        $usuario->pais = $request->pais;
        $usuario->ciudad = $request->ciudad;
        $usuario->provincia = $request->provincia;
        $usuario->cp = $request->cp;

        $usuario->save();
        return $usuario;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = User::destroy($id);
        return $usuario;
    }
}
