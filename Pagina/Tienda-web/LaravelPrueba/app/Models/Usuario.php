<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // Asegúrate de importar Notifiable

class Usuario extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'nombre',           // Nuevo atributo
        'apellido',  
        'numero_telefono',  // Nuevo atributo
        'nombre_usuario',
        'correo',
        'contraseña',
        'direccion', 
        'imagen',
        'id_rol',       // Nuevo atributo       // Nuevo atributo
        'estado',
        'created_at',
        'updated_at',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
}
