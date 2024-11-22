<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Mostrar una lista de las categorías.
     */
    public function index()
    {
        $categorias = Categoria::all();
        return response()->json($categorias, 200);
    }

    /**
     * Guardar una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre_categoria' => 'required|string|max:100',
            ]);

            $categoria = Categoria::create($request->all());
            return response()->json($categoria, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show(string $id)
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json($categoria, 200);
    }

    /**
     * Actualizar una categoría específica en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        try {
          $request->validate([
                'nombre_categoria' => 'required|string|max:100',
         ]);

            $categoria = Categoria::findOrFail($id);
            $categoria->update($request->all());

            return response()->json($categoria, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'La validación no fue exitosa'], 500);
        }
    }


    public function destroy(string $id)
{
    try {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return response()->json('La categoría ha sido eliminada', 200);
    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json(['error' => 'No se puede eliminar la categoría porque tiene productos relacionados'], 409);
    }
}


}
