<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_venta'; // Clave primaria
    public $timestamps = false; // Porque no hay created_at ni updated_at en la tabla

    // Campos asignables masivamente
    protected $fillable = [
        'id_tienda',
        'id_usuario',
        'direccion',
        'fecha_venta',
        'fecha_entrega',
        'estado',
        'MetodoPago',
        'MetodoEntrega',
        'total',
    ];

    // Relación con DetalleVenta
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    // Relación con Tienda
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda', 'id_tienda');
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
