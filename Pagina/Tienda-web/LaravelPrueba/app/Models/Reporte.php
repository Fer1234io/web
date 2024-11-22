<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'tipo_reporte',
        'parametros',
        'resultado',
        'fecha_generado',
        'id_usuario',
    ];

    protected $casts = [
        'parametros' => 'array',
        'resultado' => 'array',
    ];
}
