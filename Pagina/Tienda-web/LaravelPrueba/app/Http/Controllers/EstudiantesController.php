<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiantes;

class EstudiantesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estudiantes = Estudiantes::all();
        return response()->json($estudiantes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'carnet' => 'required|unique:estudiantes',
                'direccion' => 'required',
                'telefono' => 'required', // Verifica que este campo esté en la base de datos
                'estado' => 'required'
            ]);

            $estudiante = Estudiantes::create($request->all());
            return response()->json($estudiante, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Las validaciones fallaron'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $estudiante = Estudiantes::findOrFail($id);
        return response()->json($estudiante, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'carnet' => 'required|unique:estudiantes,carnet,' . $id,
                'direccion' => 'required',
                'telefono' => 'required', // Asegúrate de que se incluya también aquí
                'estado' => 'required'
            ]);

            $estudiante = Estudiantes::findOrFail($id);
            $estudiante->update($request->all());

            return response()->json($estudiante, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Las validaciones no han sido exitosas'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $estudiante = Estudiantes::findOrFail($id);

        $estudiante->delete();

        return response()->json('El estudiante ha sido eliminado', 200);
    }
}
