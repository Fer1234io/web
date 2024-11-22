<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generar el reporte de los 100 productos más vendidos
     * por sucursal y general.
     */
    public function topProductosMasVendidos(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $sucursal = $request->input('sucursal');
    
            if ($tipo === 'sucursal' && $sucursal) {
                // Consulta para productos más vendidos por sucursal
                $productos = DB::table('detalle_ventas')
                    ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                    ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                    ->join('tiendas', 'inventario.id_tienda', '=', 'tiendas.id_tienda') // Relación con tiendas
                    ->where('inventario.id_tienda', '=', $sucursal)
                    ->select(
                        'productos.nombre_producto as nombre_producto',
                        DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                        'tiendas.nombre_tienda as nombre_tienda'
                    )
                    ->groupBy('productos.nombre_producto', 'tiendas.nombre_tienda')
                    ->orderByDesc('total_vendido')
                    ->limit(100)
                    ->get();
            } else {
                // Consulta para productos más vendidos en general
                $productos = DB::table('detalle_ventas')
                    ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                    ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                    ->join('tiendas', 'inventario.id_tienda', '=', 'tiendas.id_tienda') // Relación con tiendas
                    ->select(
                        'productos.nombre_producto as nombre_producto',
                        DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                        'tiendas.nombre_tienda as nombre_tienda'
                    )
                    ->groupBy('productos.nombre_producto', 'tiendas.nombre_tienda')
                    ->orderByDesc('total_vendido')
                    ->limit(100)
                    ->get();
            }
    
            return response()->json([
                'success' => true,
                'data' => $productos,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    public function productosBajoInventario(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $idSucursal = $request->input('sucursal');
    
            $query = DB::table('inventario')
                ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                ->join('tiendas', 'inventario.id_tienda', '=', 'tiendas.id_tienda') // Join con tiendas
                ->select(
                    'productos.nombre_producto AS nombre_producto',
                    DB::raw('SUM(inventario.existencia) AS existencia'),
                    'tiendas.nombre_tienda AS nombre_tienda' // Nombre de la tienda
                )
                ->groupBy('productos.nombre_producto', 'inventario.id_tienda', 'tiendas.nombre_tienda', 'productos.id_producto');
    
            if ($tipo === 'general') {
                $productos = DB::table('inventario')
                    ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                    ->select(
                        'productos.nombre_producto AS nombre_producto',
                        DB::raw('SUM(inventario.existencia) AS existencia')
                    )
                    ->groupBy('productos.nombre_producto')
                    ->havingRaw('SUM(inventario.existencia) < 10')
                    ->orderBy('existencia', 'asc')
                    ->limit(20)
                    ->get();
            } elseif ($tipo === 'sucursal' && $idSucursal) {
                $productos = $query
                    ->where('inventario.id_tienda', '=', $idSucursal)
                    ->havingRaw('SUM(inventario.existencia) < 10')
                    ->orderBy('existencia', 'asc')
                    ->limit(20)
                    ->get();
            } else {
                return response()->json([
                    'error' => 'Tipo de reporte o sucursal no válido.'
                ], 400);
            }
    
            return response()->json([
                'data' => $productos,
                'message' => 'Reporte generado exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }
    



public function productosVendidosPorMes(Request $request)
{
    try {
        // Consulta para obtener los productos más vendidos por mes
        $productosPorMes = DB::table('detalle_ventas')
            ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
            ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
            ->select(
                DB::raw('MONTH(ventas.fecha_venta) AS mes'),
                'productos.nombre_producto AS nombre_producto',
                DB::raw('SUM(detalle_ventas.cantidad) AS total_vendido')
            )
            ->groupBy('mes', 'productos.nombre_producto')
            ->orderBy('mes')
            ->orderBy('total_vendido', 'desc')
            ->get();

        return response()->json([
            'data' => $productosPorMes,
            'message' => 'Reporte generado exitosamente.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Hubo un error al generar el reporte: ' . $e->getMessage()
        ], 500);
    }
}

public function productosVendidosCompleto(Request $request)
{
    try {
        $tipo = $request->input('tipo');
        $mes = $request->input('mes');
        $sucursal = $request->input('sucursal');

        if ($tipo === 'general') {
            $productosVendidos = DB::table('detalle_ventas')
                ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->select(
                    'productos.nombre_producto',
                    DB::raw('ELT(MONTH(ventas.fecha_venta), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") AS mes'),
                    DB::raw('SUM(detalle_ventas.cantidad) AS total_vendido')
                )
                ->groupBy('productos.nombre_producto', 'mes')
                ->orderBy('mes')
                ->orderBy('total_vendido', 'desc')
                ->get();
        } elseif ($tipo === 'por-mes') {
            if (!$mes) {
                return response()->json(['error' => 'El mes es requerido para este tipo de reporte.'], 400);
            }

            $productosVendidos = DB::table('detalle_ventas')
                ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->whereMonth('ventas.fecha_venta', $mes)
                ->select(
                    'productos.nombre_producto',
                    DB::raw('ELT(MONTH(ventas.fecha_venta), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") AS mes'),
                    DB::raw('SUM(detalle_ventas.cantidad) AS total_vendido')
                )
                ->groupBy('productos.nombre_producto', 'mes')
                ->orderBy('total_vendido', 'desc')
                ->get();
        } elseif ($tipo === 'por-sucursal') {
            if (!$sucursal) {
                return response()->json(['error' => 'La sucursal es requerida para este tipo de reporte.'], 400);
            }

            $productosVendidos = DB::table('detalle_ventas')
                ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario')
                ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->join('tiendas', 'inventario.id_tienda', '=', 'tiendas.id_tienda') // Unión con la tabla de tiendas
                ->where('inventario.id_tienda', $sucursal)
                ->select(
                    'productos.nombre_producto',
                    DB::raw('ELT(MONTH(ventas.fecha_venta), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") AS mes'),
                    DB::raw('SUM(detalle_ventas.cantidad) AS total_vendido'),
                    'tiendas.nombre_tienda' // Nombre de la tienda
                )
                ->groupBy('productos.nombre_producto', 'mes', 'tiendas.nombre_tienda') // Agrupar también por la tienda
                ->orderBy('total_vendido', 'desc')
                ->get();
        } else {
            return response()->json(['error' => 'Tipo de reporte no válido.'], 400);
        }

        return response()->json([
            'data' => $productosVendidos,
            'message' => 'Reporte generado exitosamente.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Hubo un error al generar el reporte: ' . $e->getMessage()
        ], 500);
    }
}


public function clientesFrecuentes(Request $request)
{
    $tipo = $request->input('tipo');
    $sucursalId = $request->input('sucursal');

    // Base de la consulta
    $query = DB::table('ventas')
        ->join('usuarios', 'ventas.id_usuario', '=', 'usuarios.id_usuario') // Relación con usuarios
        ->join('tiendas', 'ventas.id_tienda', '=', 'tiendas.id_tienda') // Relación con tiendas
        ->select(
            'usuarios.id_usuario',
            DB::raw("CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS nombre_cliente"),
            'usuarios.correo',
            DB::raw('COUNT(ventas.id_venta) AS total_compras')
        );

    if ($tipo === 'sucursal' && $sucursalId) {
        $query->addSelect('tiendas.nombre_tienda AS sucursal')
            ->where('ventas.id_tienda', $sucursalId)
            ->groupBy('usuarios.id_usuario', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.correo', 'tiendas.nombre_tienda');
    } else {
        $query->groupBy('usuarios.id_usuario', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.correo');
    }

    $data = $query->get();

    return response()->json([
        'data' => $data,
        'message' => 'Reporte generado exitosamente.',
    ]);
}


/**
 * Convertir número de mes a nombre.
 */
private function getMonthName($monthNumber)
{
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    return $months[$monthNumber] ?? 'Desconocido';
}

public function comprasPorFecha(Request $request)
{
    $fechaInicio = $request->input('fechaInicio');
    $fechaFin = $request->input('fechaFin');

    $data = DB::table('ventas')
        ->select('id_venta', 'fecha_venta', 'total')
        ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
        ->get();

    return response()->json([
        'data' => $data,
        'message' => 'Reporte generado exitosamente.',
    ]);
}

public function detalleCompra(Request $request)
{
    $idVenta = $request->input('id_venta');

    if (!$idVenta) {
        return response()->json([
            'data' => [],
            'message' => 'El ID de la venta es requerido.',
        ], 400);
    }

    // Consultar los detalles de la venta
    $detalles = DB::table('detalle_ventas')
        ->join('inventario', 'detalle_ventas.id_inventario', '=', 'inventario.id_inventario') // Relación con inventario
        ->join('productos', 'inventario.id_producto', '=', 'productos.id_producto') // Relación con productos
        ->where('detalle_ventas.id_venta', $idVenta)
        ->select(
            'productos.nombre_producto',
            'detalle_ventas.cantidad',
            'detalle_ventas.precio_unitario',
            'detalle_ventas.precio_total'
        )
        ->get();

    if ($detalles->isEmpty()) {
        return response()->json([
            'data' => [],
            'message' => 'No se encontraron detalles para la venta especificada.',
        ]);
    }

    return response()->json([
        'data' => $detalles,
        'message' => 'Detalles obtenidos exitosamente.',
    ]);
}




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
