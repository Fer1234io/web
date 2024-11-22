<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = true;

    protected $fillable = [
        'nombre_producto',
        'descripcion',
        'marca',
        'modelo',
        'fotografia_url',
        'precio',
        'estado',
        'id_categoria',
        'id_descuento',
        'created_at',
        'updated_at'
    ];

    // Relación con la categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');    }

    // Relación con inventario (si existe una tabla inventario)
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto');
    }
     // Relación con la tabla descuentos
     public function descuento()
     {
         return $this->belongsTo(Descuento::class, 'id_descuento', 'id_descuento');
     }
     
}
