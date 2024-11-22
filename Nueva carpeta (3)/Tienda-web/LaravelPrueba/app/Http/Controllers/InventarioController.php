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
            $inventario = Inventario::with(['producto' => function ($query) {
                    $query->where('estado', 1)
                          ->with(['categoria', 'descuento']); // Incluye la relación de descuento
                }, 'tienda'])
                ->where('id_tienda', $id_tienda)
                ->whereHas('producto', function ($query) {
                    $query->where('estado', 1); // Filtrar también en la relación para asegurar solo productos activos
                })
                ->get();
    
            // Procesar los productos para calcular el precio con descuento
            $inventario = $inventario->map(function ($item) {
                $producto = $item->producto;
                if ($producto && $producto->descuento && $producto->descuento->porcentaje > 0) {
                    $precioConDescuento = $producto->precio - ($producto->precio * $producto->descuento->porcentaje / 100);
                    $producto->precio_con_descuento = number_format($precioConDescuento, 2, '.', ''); // Formato con 2 decimales
                } else {
                    $producto->precio_con_descuento = $producto->precio; // Si no hay descuento, usa el precio normal
                }
                return $item;
            });
    
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
        try {
            $inventarios = Inventario::with(['producto' => function ($query) {
                    $query->where('estado', 1); // Solo productos activos
                }, 'tienda'])
                ->whereHas('producto', function ($query) {
                    $query->where('estado', 1); // Asegura que solo incluya productos activos
                })
                ->where('existencia', '>', 0) // Filtra inventarios con existencia mayor a 0
                ->get();
    
            return response()->json($inventarios, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el inventario', 'message' => $e->getMessage()], 500);
        }
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

            // Verificar si el producto ya existe en la tienda
            $existeEnTienda = Inventario::where('id_producto', $request->input('id_producto'))
                ->where('id_tienda', $request->input('id_tienda'))
                ->exists();

            if ($existeEnTienda) {
                return response()->json(['error' => 'Este producto ya tiene un registro en esta sucursal.'], 422);
            }

            // Crear el registro si no existe duplicado
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
     * Actualizar un registro específico en la base de datos.
     */
    public function update(Request $request, $id)
    {
        // Cambia `id` por `id_traslado`
        $traslado = Traslado::with('inventario')->where('id_traslado', $id)->firstOrFail();
    
        $request->validate([
            'estado' => 'required|in:En Camino,Entregado',
        ]);
    
        // Actualiza el estado y la fecha de recibido
        $traslado->estado = $request->input('estado');
        $traslado->fecha_recibido = $traslado->estado === 'Entregado' ? now() : null;
        $traslado->save();
    
        // Actualiza el inventario si es "Entregado"
        if ($traslado->estado === 'Entregado') {
            $inventarioDestino = Inventario::where('id_producto', $traslado->id_inventario)
                ->where('id_tienda', $traslado->id_destino)
                ->first();
    
            if ($inventarioDestino) {
                $inventarioDestino->existencia += $traslado->cantidad;
                $inventarioDestino->save();
            } else {
                Inventario::create([
                    'id_tienda' => $traslado->id_destino,
                    'id_producto' => $traslado->id_inventario,
                    'existencia' => $traslado->cantidad,
                ]);
            }
        }
    
        return response()->json(['mensaje' => 'Estado de traslado actualizado con éxito'], 200);
    }
        /**
 * Eliminar un registro específico de la base de datos.
 */
public function destroy($id)
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
