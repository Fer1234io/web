<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';
    protected $primaryKey = 'id_tienda';

    protected $fillable = [
        'nombre_tienda',
        'direccion',
        'ciudad',
        'departamento',
        'lat',
        'lng',
    ];

    // Relación con el modelo Inventario
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_tienda');
    }

    // Relación con el modelo Venta
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_tienda', 'id_tienda');
    }
}
