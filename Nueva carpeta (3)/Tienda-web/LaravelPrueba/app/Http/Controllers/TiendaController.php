<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
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
}
