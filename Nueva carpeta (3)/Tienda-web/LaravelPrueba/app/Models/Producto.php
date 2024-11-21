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
        'id_categoria',
        'created_at',
        'updated_at'
    ];

    // Relación con la categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    // Relación con inventario (si existe una tabla inventario)
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto');
    }
}
