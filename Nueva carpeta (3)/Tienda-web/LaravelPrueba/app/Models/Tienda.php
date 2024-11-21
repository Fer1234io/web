<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';              // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_tienda';       // Llave primaria personalizada

    protected $fillable = [
        'nombre_tienda',
        'direccion',
        'ciudad',
        'departamento',
    ];

    // Relación con el modelo Inventario
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_tienda');
    }
}
