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
Route::put('/inventario/{id}', [InventarioController::class, 'update']);   // Actualizar un registro de inventario
Route::delete('/inventario/{id}', [InventarioController::class, 'destroy']); // Eliminar un registro de inventario

Route::post('/productos/update/{id}', [ProductoController::class, 'update']);
Route::post('/inventario/update/{id}', [InventarioController::class, 'update']);

Route::get('/tiendas', [TiendaController::class, 'index']); // Listar todas las tiendas
Route::get('/tiendas/{id}', [TiendaController::class, 'show']); // Mostrar una tienda específica con inventario

// Rutas de usuarios
Route::resource('usuarios', UsuarioController::class)->except(['create', 'edit']);
Route::post('/login', [UsuarioController::class, 'login']);

//RUTA DE REGISTRO INEVTARIO
Route::get('/inventario/tienda/{id_tienda}', [InventarioController::class, 'obtenerInventarioPorTienda']);
// Ruta autenticada para obtener el Route::post('/upload-image', [InventarioController::class, 'uploadImage']);usuario actual
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//me ve?