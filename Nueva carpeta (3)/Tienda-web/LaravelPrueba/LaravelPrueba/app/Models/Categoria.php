<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';           // Nombre de la tabla
    protected $primaryKey = 'id_categoria';    // Llave primaria personalizada
    public $timestamps = false;                // Desactiva los timestamps automáticos

    protected $fillable = [
        'nombre_categoria'
    ];

    // Relación con el modelo Producto
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }
}
