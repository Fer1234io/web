<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\DetalleVenta;

class VentaController extends Controller
{
    /**
     * Listar las ventas con sus detalles.
     */
    public function index()
    {
        // Obtener todas las ventas junto con sus detalles y tienda asociada
        $ventas = Venta::with(['detalles', 'tienda'])->get();
        return response()->json($ventas, 200);
    }

    /**
     * Registrar una nueva venta.
     */
    public function store(Request $request)
    {
        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Validar los datos recibidos
            $validated = $request->validate([
                'id_tienda' => 'required|exists:tiendas,id_tienda',
                'usuario.id_usuario' => 'required|exists:usuarios,id_usuario',
                'direccion' => 'required|string|max:250',
                'MetodoPago' => 'required|string|max:50',
                'MetodoEntrega' => 'required|string|max:50',
                'total' => 'required|numeric|min:0',
                'carrito' => 'required|array|min:1',
                'carrito.*.id_inventario' => 'required|exists:inventario,id_inventario',
                'carrito.*.cantidad' => 'required|integer|min:1',
                'carrito.*.precioConDescuento' => 'required|numeric|min:0',
            ]);

            // Crear la venta
            $venta = Venta::create([
                'id_tienda' => $validated['id_tienda'],
                'id_usuario' => $validated['usuario']['id_usuario'],
                'direccion' => $validated['direccion'],
                'MetodoPago' => $validated['MetodoPago'],
                'MetodoEntrega' => $validated['MetodoEntrega'],
                'total' => $validated['total'],
                'estado' => 1, // 1 = Pendiente
                'fecha_venta' => now(), // Fecha actual
            ]);

            // Crear los detalles de la venta
            foreach ($validated['carrito'] as $producto) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_inventario' => $producto['id_inventario'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precioConDescuento'],
                    'precio_total' => $producto['cantidad'] * $producto['precioConDescuento'],
                ]);
                // Actualizar el inventario
            DB::table('inventario')
            ->where('id_inventario', $producto['id_inventario'])
            ->decrement('existencia', $producto['cantidad']);
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'message' => 'Compra realizada exitosamente.',
                'venta' => $venta
            ], 201);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            return response()->json([
                'error' => 'Ocurrió un error al procesar la compra.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar una venta específica.
     */
    public function show(string $id)
    {
        // Obtener la venta por su ID, con sus detalles y tienda asociada
        $venta = Venta::with(['detalles', 'tienda'])->find($id);

        if (!$venta) {
            return response()->json(['error' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta, 200);
    }

    /**
     * Eliminar una venta específica.
     */
    public function destroy(string $id)
    {
        try {
            $venta = Venta::findOrFail($id);
            $venta->delete();

            return response()->json(['message' => 'Venta eliminada con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
