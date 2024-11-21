<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Listar todos los roles.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    /**
     * Crear un nuevo rol.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_rol' => 'required|string|max:100',
        ]);

        $role = Role::create($request->all());
        return response()->json($role, 201);
    }

    /**
     * Mostrar un rol específico.
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role, 200);
    }

    /**
     * Actualizar un rol específico.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_rol' => 'required|string|max:100',
        ]);

        $role = Role::findOrFail($id);
        $role->update($request->all());
        return response()->json($role, 200);
    }

    /**
     * Eliminar un rol específico.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Rol eliminado correctamente'], 200);
    }
}
