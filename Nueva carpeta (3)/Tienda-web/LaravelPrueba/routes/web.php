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

Route::get('/', function () {
    return view('welcome');
});

// Rutas para cargar vistas o páginas web específicas
Route::resource('estudiantes', EstudiantesController::class);
//Route::resource('productos', ProductosController::class);
Route::resource('tiendas', TiendaController::class);
Route::resource('parcial3s', Parcial3Controller::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('inventario', InventarioController::class);

// Solo si quieres manejar `usuarios` como recurso para cargar vistas en la web
Route::resource('usuarios', UsuarioController::class);

Route::get('/inventario/tienda/{id_tienda}', [InventarioController::class, 'obtenerInventarioPorTienda']);

Route::post('/login', [UsuarioController::class, 'login']);
// routes/api.php
Route::post('/upload-image', [InventarioController::class, 'uploadImage']);


