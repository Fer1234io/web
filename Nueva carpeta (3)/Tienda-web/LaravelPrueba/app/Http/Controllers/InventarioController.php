<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    /**
     * Listar inventario por tienda específica.
     */
    public function obtenerInventarioPorTienda($id_tienda)
    {
        try {
            $inventario = Inventario::with(['producto', 'tienda'])
                ->where('id_tienda', $id_tienda)
                ->get();

            return response()->json($inventario, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el inventario', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Listar todos los registros de inventario.
     */
    public function index()
    {
        $inventarios = Inventario::with(['producto', 'tienda'])->get();
        return response()->json($inventarios);
    }

    /**
     * Guardar un nuevo registro en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_tienda' => 'required|exists:tiendas,id_tienda',
                'id_producto' => 'required|exists:productos,id_producto',
                'existencia' => 'required|integer|min:0',
            ]);

            $inventario = Inventario::create([
                'id_tienda' => $request->input('id_tienda'),
                'id_producto' => $request->input('id_producto'),
                'existencia' => $request->input('existencia'),
            ]);

            return response()->json(['mensaje' => 'Inventario creado con éxito', 'data' => $inventario], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear el inventario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el inventario', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un registro específico.
     */
    public function show(string $id)
    {
        $inventario = Inventario::with(['producto', 'tienda'])->findOrFail($id);
        return response()->json($inventario, 200);
    }

    /**
     * Actualizar un registro específico en la base de datos.
     */
    public function update(Request $request, $id)
    {
        try {
            $inventario = Inventario::findOrFail($id);

            $request->validate([
                'id_tienda' => 'required|exists:tiendas,id_tienda',
                'id_producto' => 'required|exists:productos,id_producto',
                'existencia' => 'required|integer|min:0',
            ]);

            // Asignar los valores al modelo
            $inventario->id_tienda = $request->input('id_tienda');
            $inventario->id_producto = $request->input('id_producto');
            $inventario->existencia = $request->input('existencia');

            $inventario->save();

            return response()->json(['mensaje' => 'Inventario actualizado con éxito'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el inventario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el inventario', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un registro específico de la base de datos.
     */
    public function destroy(string $id)
    {
        try {
            $inventario = Inventario::findOrFail($id);
            $inventario->delete();

            return response()->json(['mensaje' => 'Inventario eliminado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el inventario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el inventario', 'message' => $e->getMessage()], 500);
        }
    }
}
