<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcial3 extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',  
        'carnet',
        'animefavorito',
        'carrera',
        'ingefavorito',
    ];
}
