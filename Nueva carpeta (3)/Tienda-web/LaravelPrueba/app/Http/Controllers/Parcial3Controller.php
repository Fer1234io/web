<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcial3;

class Parcial3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parciales = Parcial3::all();
        return response()->json($parciales, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string',
                'carnet' => 'required|string|unique:parcial3s',
                'animefavorito' => 'required|string',
                'carrera' => 'required|string', // Cambiado a 'carrera'
                'ingefavorito' => 'required|string',
            ]);

            $parcial = Parcial3::create($request->all());
            return response()->json($parcial, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Las validaciones fallaron'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $parcial = Parcial3::findOrFail($id);
        return response()->json($parcial, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string',
                'carnet' => 'required|string|unique:parcial3s,carnet,' . $id,
                'animefavorito' => 'required|string',
                'carrera' => 'required|string', 
                'ingefavorito' => 'required|string',
            ]);

            $parcial = Parcial3::findOrFail($id);
            $parcial->update($request->all());

            return response()->json($parcial, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Las validaciones no han sido exitosas'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parcial = Parcial3::findOrFail($id);
        $parcial->delete();

        return response()->json('El registro ha sido eliminado', 200);
    }
}
