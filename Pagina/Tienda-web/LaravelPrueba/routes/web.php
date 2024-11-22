<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudiantesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\Parcial3Controller;
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


Route::get('/', function () {
    return view('welcome');
});

Route::put('admin/ventas/{id}/estado', [VentaController::class, 'actualizarEstadoVenta']);
Route::get('admin/ventas', [VentaController::class, 'obtenerVentasParaAdmin']);

Route::get('/ventas/{id_usuario}', [VentaController::class, 'obtenerVentasPorUsuario']);
Route::get('/ventas/{id_usuario}', [VentaController::class, 'obtenerVentasPorUsuario']);
Route::get('/detalle-ventas/{id_venta}', [VentaController::class, 'obtenerDetallesDeVenta']);

Route::get('/descuentos', [DescuentoController::class, 'index']);
Route::post('/descuentos', [DescuentoController::class, 'store']);
Route::get('/descuentos/{id}', [DescuentoController::class, 'show']);
Route::put('/descuentos/{id}', [DescuentoController::class, 'update']);
Route::delete('/descuentos/{id}', [DescuentoController::class, 'destroy']);
Route::post('/descuentos-reactivar/{id}', [DescuentoController::class, 'reactivate']);
Route::get('/descuentos-inactivos', [DescuentoController::class, 'getInactivos']);

Route::post('/reportes/top-productos', [ReportController::class, 'topProductosMasVendidos']);
Route::post('/reportes/bajo-inventario', [ReportController::class, 'productosBajoInventario']);
Route::post('/reportes/productos-por-mes', [ReportController::class, 'productosVendidosPorMes']);
Route::post('/reportes/productos-mas-vendidos-completo', [ReportController::class, 'productosVendidosCompleto']);
Route::post('/reportes/clientes-frecuentes', [ReportController::class, 'clientesFrecuentes']);
Route::post('/reportes/compras-por-fecha', [ReportController::class, 'comprasPorFecha']);
Route::post('/reportes/detalle-compra', [ReportController::class, 'detalleCompra']);

Route::get('/usuario-actual', [UsuarioController::class, 'usuarioActual']);
Route::get('/tienda-actual', [TiendaController::class, 'tiendaActual']);

Route::post('/detalle_ventas', [VentaController::class, 'store']);

Route::post('/ventas', [VentaController::class, 'store']);
Route::get('/tienda/direccion', [TiendaController::class, 'obtenerDireccion']);
Route::get('/tiendas/inventario/{id_inventario}', [TiendaController::class, 'obtenerTiendaPorInventario']);
Route::resource('descuentos', DescuentoController::class);
//Ruta para listar roles
Route::get('/roles', [RolController::class, 'index']);
// Ruta para listar todas las tiendas
Route::get('/tiendas', [TiendaController::class, 'index'])->name('tiendas.index');

// Ruta para mostrar una tienda específica con su inventario
Route::get('/tiendas/{id}', [TiendaController::class, 'show'])->name('tiendas.show');

// Ruta para calcular y mostrar la tienda más cercana
Route::post('/tienda-cercana', [TiendaController::class, 'tiendaCercana'])->name('tiendas.cercana');

// Rutas para cargar vistas o páginas web específicas
Route::resource('estudiantes', EstudiantesController::class);
//Route::resource('productos', ProductosController::class);
Route::resource('tiendas', TiendaController::class);
Route::resource('parcial3s', Parcial3Controller::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('inventario', InventarioController::class);
Route::put('/inventario/update/{id}', [InventarioController::class, 'updateInventario']);
Route::resource('traslados', TrasladoController::class);

// Solo si quieres manejar `usuarios` como recurso para cargar vistas en la web
Route::resource('usuarios', UsuarioController::class);
Route::post('/inventario/{id}', [InventarioController::class, 'update']);

Route::get('/inventario/tienda/{id_tienda}', [InventarioController::class, 'obtenerInventarioPorTienda']);
Route::get('/inventario/detalles/{id_tienda}', [InventarioController::class, 'obtenerDetallesInventario']);

Route::post('/usuarios', [UsuarioController::class, 'store']);
//
Route::put('{id}/rol', [UsuarioController::class, 'updateRole']);
Route::put('{id}/estado', [UsuarioController::class, 'updateEstado']);
Route::delete('{id}', [UsuarioController::class, 'destroy']);
//Actualizar solo rol de un usuario
Route::put('/usuarios/{id}/rol', [UsuarioController::class, 'updateRole']);

Route::post('/login', [UsuarioController::class, 'login']);
// routes/api.php
Route::post('/upload-image', [InventarioController::class, 'uploadImage']);


