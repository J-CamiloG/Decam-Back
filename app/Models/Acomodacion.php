<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodacion extends Model
{
    use HasFactory;
    
    protected $table = 'acomodaciones';
    
    protected $fillable = ['nombre'];
    
    // Constantes para las acomodaciones
    const SENCILLA = 1;
    const DOBLE = 2;
    const TRIPLE = 3;
    const CUADRUPLE = 4;
}