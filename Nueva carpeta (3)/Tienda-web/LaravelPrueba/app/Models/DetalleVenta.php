<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    // Especificar la tabla asociada
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id_detalle';


    // Deshabilitar los timestamps ya que la tabla no tiene `created_at` y `updated_at`
    public $timestamps = false;

    // Definir las columnas que se pueden asignar masivamente
    protected $fillable = [
        'id_venta',         // Relación con la tabla `ventas`
        'id_inventario',    // Relación con la tabla `inventario`
        'cantidad',         // Cantidad de productos vendidos
        'precio_unitario',  // Precio unitario del producto
        'precio_total',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }
    
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario', 'id_inventario');
    }
    
    
}
