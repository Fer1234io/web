<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario'; // Especifica la tabla correcta
          
    protected $primaryKey = 'id_inventario';   
    public $timestamps = true; 

    protected $fillable = [
        'id_tienda', 
        'id_producto', 
        'existencia', 
        'created_at', 
        'updated_at'
    ];

    // Relación con el modelo Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    // Relación con el modelo Tienda
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda');
    }
    
}
