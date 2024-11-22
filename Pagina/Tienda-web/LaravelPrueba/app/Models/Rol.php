<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol'; // Especifica el nombre de la clave primaria

    public $timestamps = false; 

    protected $fillable = [
        'nombre_rol'
    ];
}
