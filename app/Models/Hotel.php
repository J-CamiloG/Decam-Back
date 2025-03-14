<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;
    
    protected $table = 'hoteles';
    
    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'nit',
        'numero_habitaciones'
    ];
    
    public function habitaciones(): HasMany
    {
        return $this->hasMany(HabitacionHotel::class, 'hotel_id');
    }
}