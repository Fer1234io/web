<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;
    protected $table = 'traslados'; // Nombre de la tabla
    protected $primaryKey = 'id_traslado'; // Clave primaria personalizada
    public $timestamps = false; // Si no estás usando `created_at` y `updated_at`, configúralo en false
    
    protected $fillable = [
        'id_inventario',
        'id_tienda', // Origen
        'id_destino', // Destino
        'cantidad',
        'estado',
        'fecha_traslado',
        'fecha_recibido'
    ];

    // Relación con Inventario
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario');
    }

    // Relación con la tienda de origen
    public function tiendaOrigen()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda', 'id_tienda');
    }

    // Relación con la tienda de destino
    public function tiendaDestino()
    {
        return $this->belongsTo(Tienda::class, 'id_destino', 'id_tienda');
    }
}
