<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    /**
     * Login del usuario.
     */
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contraseña' => 'required|string',
        ]);
    
        $usuario = Usuario::with('rol')
            ->where('correo', $request->correo)
            ->where('estado', 1) // Solo usuarios activos
            ->first();
    
        if ($usuario) {
            // Verificar si la contraseña es encriptada o no
            if (\Hash::check($request->contraseña, $usuario->contraseña) || $usuario->contraseña === $request->contraseña) {
                return response()->json([
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'numero_telefono' => $usuario->numero_telefono,
                    'nombre_usuario' => $usuario->nombre_usuario,
                    'correo' => $usuario->correo,
                    'direccion' => $usuario->direccion,
                    'fotografia_url' => $usuario->imagen,
                    'rol' => $usuario->rol ? $usuario->rol->nombre_rol : null,
                ], 200);
            }
        }
    
        return response()->json(['message' => 'Credenciales incorrectas o usuario inactivo'], 401);
    }
    

    
    /**
     * Listar todos los usuarios con sus roles.
     */
    public function index()
    {
        $usuarios = Usuario::with('rol')
            ->where('estado', 1) // Filtrar usuarios activos
            ->get();
    
        return response()->json($usuarios, 200);
    }
    

    /**
     * Crear un nuevo usuario.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'correo' => 'required|string|email|max:100|unique:usuarios',
                'numero_telefono' => 'required|string|max:15',
                'nombre_usuario' => 'required|string|max:100|unique:usuarios',
                'direccion' => 'required|string|max:255',
                'id_rol' => 'required|integer|exists:roles,id_rol',
                'contraseña' => 'required|string|min:6',
                'imagen' => 'nullable|string',
            ]);
    
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'numero_telefono' => $request->numero_telefono,
                'nombre_usuario' => $request->nombre_usuario,
                'direccion' => $request->direccion,
                'id_rol' => $request->id_rol,
                'contraseña' => bcrypt($request->contraseña),
                'imagen' => $request->imagen,
                'estado' => 1,
            ]);
    
            return response()->json(['message' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Mostrar un usuario específico con su rol.
     */
    public function show(string $id)
    {
        $usuario = Usuario::with('rol')->findOrFail($id);
    
        return response()->json([
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'numero_telefono' => $usuario->numero_telefono,
            'nombre_usuario' => $usuario->nombre_usuario,
            'correo' => $usuario->correo,
            'direccion' => $usuario->direccion,
            'fotografia_url' => $usuario->imagen,
            'rol' => $usuario->rol ? $usuario->rol->nombre_rol : null // Maneja si no hay rol
        ], 200);
    }
    
    public function usuarioActual()
    {
        // Supongamos que el usuario está autenticado
        return response()->json(auth()->user(), 200);
    }
    
    /**
     * Actualizar un usuario específico.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'string|max:100',
                'apellido' => 'string|max:100',
                'correo' => 'string|email|max:100|unique:usuarios,correo,' . $id . ',id_usuario',
                'numero_telefono' => 'string|max:15',
                'direccion' => 'string|max:255',
                'imagen' => 'nullable|string',
            ]);
    
            $usuario = Usuario::findOrFail($id);
    
            $usuario->update([
                'nombre' => $request->nombre ?? $usuario->nombre,
                'apellido' => $request->apellido ?? $usuario->apellido,
                'correo' => $request->correo ?? $usuario->correo,
                'numero_telefono' => $request->numero_telefono ?? $usuario->numero_telefono,
                'direccion' => $request->direccion ?? $usuario->direccion,
                'imagen' => $request->imagen ?? $usuario->imagen,
            ]);
    
            return response()->json([
                'message' => 'Usuario actualizado con éxito',
                'usuario' => $usuario,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno', 'message' => $e->getMessage()], 500);
        }
    }
        /**
 * Actualizar el rol de un usuario.
 */
public function updateRole(Request $request, $id)
{
    $request->validate([
        'id_rol' => 'required|integer|exists:roles,id_rol',
    ]);

    $usuario = Usuario::findOrFail($id);
    $usuario->id_rol = $request->id_rol;
    $usuario->save();

    return response()->json(['message' => 'Rol actualizado correctamente.'], 200);
}
public function updateEstado(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|boolean', // Asegura que sea 0 o 1
    ]);

    $usuario = Usuario::findOrFail($id);
    $usuario->estado = $request->estado;
    $usuario->save();

    return response()->json(['message' => 'Estado actualizado correctamente.'], 200);
}



    /**
     * Eliminar un usuario específico.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar el usuario por su ID
            $usuario = Usuario::findOrFail($id);
    
            // Cambiar el estado a 0 (inactivo)
            $usuario->estado = 0;
            $usuario->save();
    
            // Respuesta de éxito
            return response()->json(['message' => 'Usuario desactivado correctamente'], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => 'Error al desactivar el usuario: ' . $e->getMessage()], 500);
        }
    }
    
}
