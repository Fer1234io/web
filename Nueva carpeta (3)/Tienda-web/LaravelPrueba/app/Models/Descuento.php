<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    use HasFactory;

    protected $table = 'descuentos'; // Nombre de la tabla
    protected $primaryKey = 'id_descuento'; // Clave primaria personalizada
    public $timestamps = true;

    protected $fillable = [
        'nombre',        // Nombre del descuento
        'descripcion',   // Descripción del descuento (opcional)
        'porcentaje',    // Porcentaje de descuento
        'estado',        // Estado: 1 (activo), 0 (inactivo)
        'created_at',
        'updated_at'
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_descuento', 'id_descuento');
    }
}
