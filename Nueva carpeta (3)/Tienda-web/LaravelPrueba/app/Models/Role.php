<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles'; // Nombre de la tabla
    protected $primaryKey = 'id_rol'; // Llave primaria
    public $timestamps = false; // La tabla no tiene columnas `created_at` o `updated_at`

    protected $fillable = [
        'nombre_rol',
    ];
}
