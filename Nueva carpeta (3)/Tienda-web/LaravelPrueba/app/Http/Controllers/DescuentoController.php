<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Descuento;

class DescuentoController extends Controller
{
    /**
     * Listar todos los descuentos activos (estado = 1).
     */
    public function index()
    {
        $descuentos = Descuento::where('estado', 1)->get();
        return response()->json($descuentos);
    }

    /**
     * Crear un nuevo descuento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        $descuento = Descuento::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'porcentaje' => $request->input('porcentaje'),
            'estado' => 1, // Siempre activo al crear
        ]);

        return response()->json(['mensaje' => 'Descuento creado con éxito', 'descuento' => $descuento], 201);
    }

    /**
     * Mostrar un descuento específico.
     */
    public function show($id)
    {
        $descuento = Descuento::findOrFail($id);
        return response()->json($descuento);
    }

    /**
     * Actualizar un descuento específico.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        $descuento = Descuento::findOrFail($id);

        $descuento->update([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'porcentaje' => $request->input('porcentaje'),
            'estado' => 1, // Mantener activo siempre al actualizar
        ]);

        return response()->json(['mensaje' => 'Descuento actualizado con éxito', 'descuento' => $descuento]);
    }

    /**
     * Desactivar un descuento (cambiar estado a 0).
     */
    public function destroy($id)
    {
        $descuento = Descuento::findOrFail($id);
        $descuento->update(['estado' => 0]); // Cambiar estado a inactivo
        return response()->json(['mensaje' => 'Descuento desactivado con éxito']);
    }
    public function reactivate($id)
{
    $descuento = Descuento::findOrFail($id);
    $descuento->update(['estado' => 1]); // Cambiar estado a activo
    return response()->json(['mensaje' => 'Descuento reactivado con éxito']);
}
/**
 * Listar todos los descuentos inactivos (estado = 0).
 */
public function getInactivos()
{
    $descuentos = Descuento::where('estado', 0)->get();
    return response()->json($descuentos);
}

}
