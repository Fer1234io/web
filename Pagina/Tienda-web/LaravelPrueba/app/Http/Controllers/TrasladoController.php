<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Traslado;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class TrasladoController extends Controller
{
    /**
     * Listar todos los traslados.
     */
    public function index(Request $request)
    {
        // Crear la consulta base con las relaciones necesarias
        $query = Traslado::with(['tiendaOrigen', 'tiendaDestino', 'inventario.producto']);
    
        // Filtrar por id_destino si se proporciona en la solicitud
        if ($request->has('id_destino') && $request->id_destino) {
            $query->where('id_destino', $request->id_destino);
        }
    
        // Filtrar por estado si se proporciona en la solicitud
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
    
        // Ejecutar la consulta y obtener los resultados
        $traslados = $query->get();
    
        return response()->json($traslados, 200);
    }
    

    /**
     * Crear un nuevo traslado.
     */
/**
     * Crear un nuevo traslado.
     */
    public function store(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'id_inventario' => 'required|exists:inventario,id_inventario',
                'tienda_origen' => 'required|exists:tiendas,id_tienda',
                'tienda_destino' => 'required|exists:tiendas,id_tienda|different:tienda_origen',
                'cantidad' => 'required|integer|min:1',
                'fecha_traslado' => 'required|date',
                'estado' => 'required|string',
            ]);
    
            $inventario = Inventario::findOrFail($request->input('id_inventario'));
    
            // Verificar si hay suficiente cantidad en la tienda de origen
            if ($inventario->existencia < $request->input('cantidad')) {
                return response()->json(['error' => 'No hay suficiente cantidad en la tienda de origen.'], 422);
            }
    
            // Determinar la fecha de recibido según el estado
            $fechaRecibido = $request->input('estado') === 'En Camino' ? null : $request->input('fecha_recibido');
    
            // Crear el traslado
            $traslado = Traslado::create([
                'id_inventario' => $request->input('id_inventario'),
                'id_tienda' => $request->input('tienda_origen'),
                'id_destino' => $request->input('tienda_destino'),
                'cantidad' => $request->input('cantidad'),
                'estado' => $request->input('estado'),
                'fecha_traslado' => $request->input('fecha_traslado'),
                'fecha_recibido' => $fechaRecibido,
            ]);
            
    
            // Restar la cantidad en el inventario de la tienda de origen
            $inventario->existencia -= $request->input('cantidad');
            $inventario->save();
    
            return response()->json(['mensaje' => 'Traslado creado con éxito', 'data' => $traslado], 201);
    
        } catch (\Exception $e) {
            Log::error('Error al crear el traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el traslado', 'message' => $e->getMessage()], 500);
        }
    }
    

    public function show($id)
    {
        $traslado = Traslado::with(['tiendaOrigen', 'tiendaDestino', 'inventario'])->findOrFail($id);
        return response()->json($traslado, 200);
    }

    /**
     * Actualizar el estado del traslado a "Entregado".
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar el traslado por su ID
            $traslado = Traslado::findOrFail($id);
    
            // Validar que el estado proporcionado sea "Entregado"
            $request->validate([
                'estado' => 'required|string|in:Entregado',
            ]);
    
            // Actualizar el estado y la fecha de recibido
            $traslado->estado = $request->input('estado');
            $traslado->fecha_recibido = now();
            $traslado->save();
    
            // Buscar si el producto ya existe en el inventario de la tienda destino
            $inventario = Inventario::where('id_tienda', $traslado->id_destino)
                ->where('id_producto', $traslado->inventario->id_producto)
                ->first();
    
            if ($inventario) {
                // Si el registro ya existe, sumar la cantidad al inventario
                $inventario->existencia += $traslado->cantidad;
                $inventario->save();
            } else {
                // Si el registro no existe, crear un nuevo registro en inventario
                Inventario::create([
                    'id_tienda' => $traslado->id_destino,
                    'id_producto' => $traslado->inventario->id_producto,
                    'existencia' => $traslado->cantidad,
                ]);
            }
    
            return response()->json(['mensaje' => 'Traslado actualizado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el traslado', 'message' => $e->getMessage()], 500);
        }
    }
      
    /**
     * Eliminar un traslado.
     */
    public function destroy($id)
    {
        try {
            $traslado = Traslado::findOrFail($id);
            $traslado->delete();

            return response()->json(['mensaje' => 'Traslado eliminado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el traslado', 'message' => $e->getMessage()], 500);
        }
    }
}
