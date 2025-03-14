<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HotelController;
use App\Http\Controllers\API\HabitacionHotelController;

// Ruta de prueba
Route::get('test', function() {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// Rutas para hoteles
Route::apiResource('hoteles', HotelController::class);

// Rutas para habitaciones
Route::apiResource('habitaciones', HabitacionHotelController::class);
Route::get('hoteles/{hotel}/habitaciones', [HabitacionHotelController::class, 'getByHotel']);

// Rutas para obtener catálogos
Route::get('tipos-habitacion', function() {
    return response()->json(['data' => App\Models\TipoHabitacion::all()]);
});

Route::get('acomodaciones', function() {
    return response()->json(['data' => App\Models\Acomodacion::all()]);
});