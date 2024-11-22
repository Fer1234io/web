<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudiantesController;
use App\Http\Controllers\Parcial3Controller;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TrasladoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\ReportController;

Route::put('admin/ventas/{id}/estado', [VentaController::class, 'actualizarEstadoVenta']);
Route::get('admin/ventas', [VentaController::class, 'obtenerVentasParaAdmin']);

Route::post('/reportes/top-productos', [ReportController::class, 'topProductosMasVendidos']);
Route::post('/reportes/bajo-inventario', [ReportController::class, 'productosBajoInventario']);
Route::post('/reportes/productos-por-mes', [ReportController::class, 'productosVendidosPorMes']);
Route::post('/reportes/productos-mas-vendidos-completo', [ReportController::class, 'productosVendidosCompleto']);
Route::get('/usuario-actual', [UsuarioController::class, 'usuarioActual']);
Route::get('/tienda-actual', [TiendaController::class, 'tiendaActual']);
Route::post('/reportes/clientes-frecuentes', [ReportController::class, 'clientesFrecuentes']);
Route::post('/reportes/compras-por-fecha', [ReportController::class, 'comprasPorFecha']);
Route::post('/reportes/detalle-compra', [ReportController::class, 'detalleCompra']);

Route::resource('ventas', VentaController::class);
Route::resource('detalle_ventas', VentaController::class);
Route::get('/ventas/{id_usuario}', [VentaController::class, 'obtenerVentasPorUsuario']);
Route::get('/detalle-ventas/{id_venta}', [VentaController::class, 'obtenerDetallesDeVenta']);

Route::resource('productos', ProductoController::class);
Route::resource('descuentos', DescuentoController::class);
Route::get('/tienda/direccion', [TiendaController::class, 'obtenerDireccion']);

Route::resource('traslados', TrasladoController::class);
Route::get('/traslados', [EstudiantesController::class, 'index']);
Route::post('/traslados', [EstudiantesController::class, 'store']);
Route::put('/traslados/{id}', [EstudiantesController::class, 'update']);
Route::delete('/traslados/{id}', [EstudiantesController::class, 'destroy']);

// Rutas para estudiantes
Route::resource('estudiantes', EstudiantesController::class);
Route::get('/estudiantes', [EstudiantesController::class, 'index']);
Route::post('/estudiantes', [EstudiantesController::class, 'store']);
Route::put('/estudiantes/{id}', [EstudiantesController::class, 'update']);
Route::delete('/estudiantes/{id}', [EstudiantesController::class, 'destroy']);

// Rutas para parcial3
Route::resource('parcial3s', Parcial3Controller::class);
Route::get('/parcial3s', [Parcial3Controller::class, 'index']);
Route::post('/parcial3s', [Parcial3Controller::class, 'store']);
Route::put('/parcial3s/{id}', [Parcial3Controller::class, 'update']);
Route::delete('/parcial3s/{id}', [Parcial3Controller::class, 'destroy']);

// Rutas para Categorias
Route::get('/categorias', [CategoriaController::class, 'index']);         
Route::post('/categorias', [CategoriaController::class, 'store']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);     
Route::put('/categorias/{id}', [CategoriaController::class, 'update']); 
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

Route::get('/tiendas/inventario/{id_inventario}', [TiendaController::class, 'obtenerTiendaPorInventario']);

// Rutas para Productos
Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

// Rutas para Inventario
Route::get('/inventario', [InventarioController::class, 'index']);         // Listar todos los registros de inventario
Route::post('/inventario', [InventarioController::class, 'store']);        // Crear un nuevo registro en inventario
Route::get('/inventario/{id}', [InventarioController::class, 'show']);     // Mostrar un registro específico de inventario
Route::post('/inventario/{id}', [InventarioController::class, 'update']);
Route::delete('/inventario/{id}', [InventarioController::class, 'destroy']); // Eliminar un registro de inventario
Route::put('/inventario/update/{id}', [InventarioController::class, 'updateInventario']);

Route::post('/productos/update/{id}', [ProductoController::class, 'update']);
Route::post('/inventario/update/{id}', [InventarioController::class, 'update']);

Route::get('/tiendas', [TiendaController::class, 'index']); // Listar todas las tiendas
Route::get('/tiendas/{id}', [TiendaController::class, 'show']); // Mostrar una tienda específica con inventario

// Rutas de usuarios
Route::resource('usuarios', UsuarioController::class)->except(['create', 'edit']);
Route::post('/login', [UsuarioController::class, 'login']);
Route::put('{id}/rol', [UsuarioController::class, 'updateRole']);
Route::put('{id}/estado', [UsuarioController::class, 'updateEstado']);
Route::delete('{id}', [UsuarioController::class, 'destroy']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
// RUTAS PARA LOS ROLES
Route::get('/roles', [RolController::class, 'index']);

//RUTA DE REGISTRO INEVTARIO
Route::get('/inventario/tienda/{id_tienda}', [InventarioController::class, 'obtenerInventarioPorTienda']);
Route::get('/inventario/detalles/{id_tienda}', [InventarioController::class, 'obtenerDetallesInventario']);

Route::get('/descuentos', [DescuentoController::class, 'index']);
Route::post('/descuentos', [DescuentoController::class, 'store']);
Route::get('/descuentos/{id}', [DescuentoController::class, 'show']);
Route::put('/descuentos/{id}', [DescuentoController::class, 'update']);
Route::delete('/descuentos/{id}', [DescuentoController::class, 'destroy']);
Route::post('/descuentos-reactivar/{id}', [DescuentoController::class, 'reactivate']);
Route::get('/descuentos-inactivos', [DescuentoController::class, 'getInactivos']);

// Ruta autenticada para obtener el Route::post('/upload-image', [InventarioController::class, 'uploadImage']);usuario actual
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
