<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/login',
        'tiendas',
        'tiendas/*',
        'descuentos',
        'descuentos/*',
        'descuentos-reactivar/*',
        '/tienda-cercana',
        'estudiantes',
        'productos',
        'productos/*',
        'categorias',
        'categorias/*',
        'inventario',
        'inventario/*',
        'usuarios',
        'usuarios/*',
        'traslados',
        'traslados/*', 
        'ventas',
        'ventas/*',
        'detalle_ventas',
        'detalle_ventas/*',
        'reportes',
        'reportes/*',
        'reportes/generar',
        '/reportes/bajo-inventario',
        '/reportes/productos-por-mes',
        '/reportes/productos-mas-vendidos-completo',
        '/reportes/clientes-frecuentes',
        '/reportes/compras-por-fecha',
        '/reportes/detalle-compra',
        'admin/ventas',
        'admin/ventas/*',
        'admin/ventas/{id}/estado', 
    ];    
}
