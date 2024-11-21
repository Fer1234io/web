<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    // Login del usuario
    public function login(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'correo' => 'required|email',
            'contraseña' => 'required|string',
        ]);
    
        // Busca al usuario por correo
        $usuario = Usuario::with('rol')->where('correo', $request->correo)->first();
    
        // Depuración: Si no se encuentra el usuario
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    
        // Depuración: Verifica valores del usuario
        return response()->json([
            'correo' => $usuario->correo,
            'contraseña_en_bd' => $usuario->contraseña,
            'contraseña_ingresada' => $request->contraseña,
            'estado' => $usuario->estado,
            'tipo_estado' => gettype($usuario->estado),
        ]);
    
        // Verifica la contraseña
        if ($usuario->contraseña !== $request->contraseña) {
            return response()->json([
                'message' => 'Contraseña incorrecta'
            ], 401);
        }
    
        // Verifica si el usuario está activo
        if ($usuario->estado != 1) {
            return response()->json([
                'message' => 'El usuario está inactivo. Contacte al administrador.'
            ], 403);
        }
    
        // Respuesta exitosa
        return response()->json([
            'id_usuario' => $usuario->id_usuario,
            'nombre_usuario' => $usuario->nombre_usuario,
            'correo' => $usuario->correo,
            'rol' => $usuario->rol->nombre_rol
        ], 200);
    }
    
    

    // Listar todos los usuarios
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        return response()->json($usuarios, 200);
    }

    // Crear un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:100',
            'correo' => 'required|string|email|max:100|unique:usuarios',
            'contraseña' => 'required|string|min:6',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ]);

        $usuario = Usuario::create($request->all());
        return response()->json($usuario, 201);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $usuario = Usuario::with('rol')->findOrFail($id);
        return response()->json($usuario, 200);
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_usuario' => 'string|max:100',
            'correo' => 'string|email|max:100|unique:usuarios,correo,' . $id . ',id_usuario',
            'id_rol' => 'integer|exists:roles,id_rol',
        ]);

        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->all());

        return response()->json($usuario, 200);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}
