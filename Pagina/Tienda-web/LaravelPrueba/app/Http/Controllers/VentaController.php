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
    public function obtenerVentasPorUsuario($id_usuario)
{
    try {
        $ventas = Venta::select('id_venta', 'fecha_venta', 'total', 'estado', 'metodopago')
            ->where('id_usuario', $id_usuario)
            ->orderBy('fecha_venta', 'desc')
            ->get();

        if ($ventas->isEmpty()) {
            return response()->json([], 200); // Retorna un array vacío
        }

        return response()->json($ventas, 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al obtener ventas',
            'message' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Obtener los detalles de una venta específica.
     */
    public function obtenerDetallesDeVenta($id_venta)
    {
        try {
            // Obtener los detalles de la venta junto con el nombre del producto desde la tabla productos
            $detalles = DB::table('detalle_ventas')
                ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                ->select(
                    'detalle_ventas.id_detalle',
                    'detalle_ventas.cantidad',
                    'detalle_ventas.precio_unitario',
                    'detalle_ventas.precio_total',
                    'productos.nombre_producto'
                )
                ->where('detalle_ventas.id_venta', $id_venta)
                ->get();
    
            if ($detalles->isEmpty()) {
                return response()->json(['message' => 'No se encontraron detalles para esta venta.'], 404);
            }
    
            return response()->json($detalles, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener detalles de la venta',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function obtenerVentasParaAdmin(Request $request)
    {
        try {
            $estado = $request->query('estado'); // Obtener el filtro de estado

            $ventasQuery = Venta::with(['detalles', 'tienda', 'usuario']); // Relación con usuario, tienda, etc.

            if (!is_null($estado)) {
                $ventasQuery->where('estado', $estado);
            }

            $ventas = $ventasQuery->orderBy('fecha_venta', 'desc')->get();

            return response()->json($ventas, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las ventas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar el estado de una venta específica para administración.
     */
    public function actualizarEstadoVenta(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'estado' => 'required|in:0,1,2', // 0 = Entregado, 1 = Pendiente, 2 = Cancelado
        ]);

        $venta = Venta::findOrFail($id);

        // Actualizar el estado
        $venta->estado = $validated['estado'];

        // Si el estado es "Entregado", establecer la fecha de entrega como la fecha actual
        if ($validated['estado'] == 0) {
            $venta->fecha_entrega = now(); // Usar la fecha y hora actuales
        } else {
            $venta->fecha_entrega = null; // Limpiar la fecha de entrega si el estado no es entregado
        }

        $venta->save();

        return response()->json([
            'message' => 'Estado de la venta actualizado correctamente',
            'venta' => $venta,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al actualizar el estado de la venta',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
