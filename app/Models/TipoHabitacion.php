<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;
    
    protected $table = 'tipos_habitacion';
    
    protected $fillable = ['nombre'];
    
    // Constantes para los tipos de habitación
    const ESTANDAR = 1;
    const JUNIOR = 2;
    const SUITE = 3;
}