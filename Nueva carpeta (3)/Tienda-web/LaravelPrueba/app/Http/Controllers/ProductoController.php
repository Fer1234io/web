<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    /**
     * Listar todos los productos.
     */
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        return response()->json($productos);
    }

    /**
     * Guardar un nuevo producto en la base de datos.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre_producto' => 'nullable|string',
                'descripcion' => 'nullable|string',
                'marca' => 'nullable|string',
                'modelo' => 'nullable|string',
                'precio' => 'nullable|numeric',
                'id_categoria' => 'nullable|exists:categorias,id_categoria',
            ]);

            // Validación condicional para fotografia_url
            if ($request->hasFile('fotografia_url')) {
                $request->validate([
                    'fotografia_url' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
            } elseif ($request->filled('fotografia_url')) {
                $request->validate([
                    'fotografia_url' => 'string|max:65535',
                ]);
            }

            // Procesar la imagen
            $imageBase64 = null;
            if ($request->hasFile('fotografia_url')) {
                $image = $request->file('fotografia_url');
                $imageBase64 = base64_encode(file_get_contents($image->path()));
            } elseif ($request->filled('fotografia_url')) {
                $imageBase64 = $request->input('fotografia_url');
            }

            // Crear el producto con la imagen en Base64
            $producto = Producto::create([
                'nombre_producto' => $request->input('nombre_producto'),
                'descripcion' => $request->input('descripcion'),
                'marca' => $request->input('marca'),
                'modelo' => $request->input('modelo'),
                'fotografia_url' => $imageBase64,
                'precio' => $request->input('precio'),
                'id_categoria' => $request->input('id_categoria'),
            ]);

            return response()->json(['mensaje' => 'Producto creado con éxito'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear el producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el producto', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un producto específico.
     */
    public function show(string $id)
    {
        $producto = Producto::findOrFail($id);
        return response()->json($producto, 200);
    }

    /**
     * Actualizar un producto específico en la base de datos.
     */
    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $request->validate([
                'nombre_producto' => 'required|string',
                'descripcion' => 'required|string',
                'marca' => 'required|string',
                'modelo' => 'required|string',
                'precio' => 'required|numeric',
                'id_categoria' => 'required|exists:categorias,id_categoria',
            ]);

            // Validación condicional para fotografia_url
            if ($request->hasFile('fotografia_url')) {
                $request->validate([
                    'fotografia_url' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
            } elseif ($request->filled('fotografia_url')) {
                $request->validate([
                    'fotografia_url' => 'string|max:65535',
                ]);
            }

            // Asignar los valores al modelo
            $producto->nombre_producto = $request->input('nombre_producto');
            $producto->descripcion = $request->input('descripcion');
            $producto->marca = $request->input('marca');
            $producto->modelo = $request->input('modelo');
            $producto->precio = $request->input('precio');
            $producto->id_categoria = $request->input('id_categoria');

            // Procesar la imagen si es proporcionada
            if ($request->hasFile('fotografia_url')) {
                $image = $request->file('fotografia_url');
                $producto->fotografia_url = base64_encode(file_get_contents($image->path()));
            } elseif ($request->filled('fotografia_url')) {
                $producto->fotografia_url = $request->input('fotografia_url');
            }

            $producto->save();

            return response()->json(['mensaje' => 'Producto actualizado con éxito'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el producto', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un producto específico de la base de datos.
     */
    public function destroy(string $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return response()->json(['mensaje' => 'Producto eliminado con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el producto', 'message' => $e->getMessage()], 500);
        }
    }
}
