<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;
use App\Models\Inventario;

class TiendaController extends Controller
{
    public function tiendaActual()
{
    // Supongamos que estás retornando la tienda seleccionada
    $tienda = Tienda::find(session('tienda_actual_id')); // Cambiar según tu lógica
    return response()->json($tienda, 200);
}

    public function obtenerTiendaPorInventario($id_inventario)
    {
        try {
            // Buscar en la tabla de inventario
            $inventario = Inventario::where('id_inventario', $id_inventario)->first();
    
            // Validar si existe el inventario
            if (!$inventario) {
                return response()->json(['error' => 'No se encontró un inventario con el ID proporcionado.'], 404);
            }
    
            // Buscar la tienda asociada
            $tienda = Tienda::find($inventario->id_tienda);
    
            // Validar si existe la tienda
            if (!$tienda) {
                return response()->json(['error' => 'No se encontró la tienda asociada al inventario.'], 404);
            }
    
            // Retornar los datos de la tienda
            return response()->json(['tienda' => $tienda], 200);
        } catch (\Exception $e) {
            \Log::error("Error al obtener la tienda por inventario: " . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud.'], 500);
        }
    }
    

    // Listar todas las tiendas
    public function index()
    {
        return Tienda::all();
    }

    // Mostrar una tienda específica con inventario
    public function show($id)
    {
        $tienda = Tienda::with('inventarios.producto')->find($id);
        if (!$tienda) {
            return response()->json(['error' => 'Tienda no encontrada'], 404);
        }
        return $tienda;
    }

    // Calcular y devolver la tienda más cercana
    public function tiendaCercana(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
    
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
    
        $tiendas = Tienda::all();
    
        $tiendas = $tiendas->map(function ($tienda) use ($userLat, $userLng) {
            $tienda->distancia = $this->calcularDistancia($userLat, $userLng, $tienda->lat, $tienda->lng);
            return $tienda;
        });
    
        $tiendasOrdenadas = $tiendas->sortBy('distancia')->values(); // Ordenar y reindexar
        $tiendaCercana = $tiendasOrdenadas->first();
    
        return response()->json([
            'tiendas' => $tiendasOrdenadas,
            'tiendaCercana' => $tiendaCercana,
        ]);
    }
    
    // Método para calcular la distancia entre dos puntos GPS
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c; // Retorna la distancia en kilómetros
    }
}
