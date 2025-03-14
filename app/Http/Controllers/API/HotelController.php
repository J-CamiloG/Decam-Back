<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function index()
    {
        $hoteles = Hotel::with('habitaciones.tipoHabitacion', 'habitaciones.acomodacion')->get();
        return response()->json(['data' => $hoteles], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:hoteles,nombre',
            'direccion' => 'required|string',
            'ciudad' => 'required|string',
            'nit' => 'required|string|unique:hoteles,nit',
            'numero_habitaciones' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hotel = Hotel::create($request->all());
        return response()->json(['data' => $hotel, 'message' => 'Hotel creado con éxito'], 201);
    }

    public function show($id)
    {
        $hotel = Hotel::with('habitaciones.tipoHabitacion', 'habitaciones.acomodacion')->find($id);
        
        if (!$hotel) {
            return response()->json(['message' => 'Hotel no encontrado'], 404);
        }
        
        return response()->json(['data' => $hotel], 200);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        
        if (!$hotel) {
            return response()->json(['message' => 'Hotel no encontrado'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|unique:hoteles,nombre,' . $id,
            'direccion' => 'sometimes|required|string',
            'ciudad' => 'sometimes|required|string',
            'nit' => 'sometimes|required|string|unique:hoteles,nit,' . $id,
            'numero_habitaciones' => 'sometimes|required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $hotel->update($request->all());
        return response()->json(['data' => $hotel, 'message' => 'Hotel actualizado con éxito'], 200);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        
        if (!$hotel) {
            return response()->json(['message' => 'Hotel no encontrado'], 404);
        }
        
        $hotel->delete();
        return response()->json(['message' => 'Hotel eliminado con éxito'], 200);
    }
}