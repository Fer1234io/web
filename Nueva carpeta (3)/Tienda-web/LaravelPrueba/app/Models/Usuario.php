<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'nombre_usuario',
        'correo',
        'contraseña',
        'id_rol',
        'estado',
    ];

    protected $casts = [
        'estado' => 'integer',
    ];
    

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
}
