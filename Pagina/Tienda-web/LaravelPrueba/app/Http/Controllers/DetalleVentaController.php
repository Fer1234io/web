<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleVenta;

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalles = DetalleVenta::with('venta', 'inventario')->get();
        return response()->json($detalles, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $detalleVenta = DetalleVenta::create([
                'id_venta' => $request->id_venta,
                'id_inventario' => $request->id_inventario,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
            ]);

            return response()->json(['message' => 'Detalle de venta creado con éxito', 'detalle' => $detalleVenta], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detalle = DetalleVenta::with('venta', 'inventario')->find($id);

        if (!$detalle) {
            return response()->json(['error' => 'Detalle de venta no encontrado'], 404);
        }

        return response()->json($detalle, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $detalle = DetalleVenta::findOrFail($id);
            $detalle->delete();

            return response()->json(['message' => 'Detalle de venta eliminado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
