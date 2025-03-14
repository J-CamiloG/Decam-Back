<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HabitacionHotel;
use App\Models\TipoHabitacion;
use App\Models\Acomodacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HabitacionHotelController extends Controller
{
    public function index()
    {
        $habitaciones = HabitacionHotel::with('hotel', 'tipoHabitacion', 'acomodacion')->get();
        return response()->json(['data' => $habitaciones], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hoteles,id',
            'tipo_habitacion_id' => 'required|exists:tipos_habitacion,id',
            'acomodacion_id' => 'required|exists:acomodaciones,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validar la combinación de tipo de habitación y acomodación
        if (!$this->validarTipoAcomodacion($request->tipo_habitacion_id, $request->acomodacion_id)) {
            return response()->json([
                'message' => 'La combinación de tipo de habitación y acomodación no es válida'
            ], 422);
        }

        // Verificar que no se supere el número máximo de habitaciones del hotel
        $hotel = Hotel::find($request->hotel_id);
        $totalHabitacionesActuales = HabitacionHotel::where('hotel_id', $request->hotel_id)
            ->sum('cantidad');
        
        if ($totalHabitacionesActuales + $request->cantidad > $hotel->numero_habitaciones) {
            return response()->json([
                'message' => 'La cantidad de habitaciones supera el máximo permitido para este hotel'
            ], 422);
        }

        // Verificar que no exista la misma combinación para el hotel
        $existente = HabitacionHotel::where('hotel_id', $request->hotel_id)
            ->where('tipo_habitacion_id', $request->tipo_habitacion_id)
            ->where('acomodacion_id', $request->acomodacion_id)
            ->first();
            
        if ($existente) {
            return response()->json([
                'message' => 'Ya existe esta combinación de tipo de habitación y acomodación para este hotel'
            ], 422);
        }

        $habitacion = HabitacionHotel::create($request->all());
        return response()->json(['data' => $habitacion, 'message' => 'Habitación agregada con éxito'], 201);
    }

    public function show($id)
    {
        $habitacion = HabitacionHotel::with('hotel', 'tipoHabitacion', 'acomodacion')->find($id);
        
        if (!$habitacion) {
            return response()->json(['message' => 'Habitación no encontrada'], 404);
        }
        
        return response()->json(['data' => $habitacion], 200);
    }

    public function update(Request $request, $id)
    {
        $habitacion = HabitacionHotel::find($id);
        
        if (!$habitacion) {
            return response()->json(['message' => 'Habitación no encontrada'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'sometimes|required|exists:hoteles,id',
            'tipo_habitacion_id' => 'sometimes|required|exists:tipos_habitacion,id',
            'acomodacion_id' => 'sometimes|required|exists:acomodaciones,id',
            'cantidad' => 'sometimes|required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Si se están cambiando el tipo o la acomodación, validar la combinación
        $tipoHabitacionId = $request->tipo_habitacion_id ?? $habitacion->tipo_habitacion_id;
        $acomodacionId = $request->acomodacion_id ?? $habitacion->acomodacion_id;
        
        if (!$this->validarTipoAcomodacion($tipoHabitacionId, $acomodacionId)) {
            return response()->json([
                'message' => 'La combinación de tipo de habitación y acomodación no es válida'
            ], 422);
        }

        // Verificar que no se supere el número máximo de habitaciones del hotel
        $hotelId = $request->hotel_id ?? $habitacion->hotel_id;
        $hotel = Hotel::find($hotelId);
        
        $totalHabitacionesActuales = HabitacionHotel::where('hotel_id', $hotelId)
            ->where('id', '!=', $id)
            ->sum('cantidad');
        
        $nuevaCantidad = $request->cantidad ?? $habitacion->cantidad;
        
        if ($totalHabitacionesActuales + $nuevaCantidad > $hotel->numero_habitaciones) {
            return response()->json([
                'message' => 'La cantidad de habitaciones supera el máximo permitido para este hotel'
            ], 422);
        }

        // Verificar que no exista la misma combinación para el hotel (si se está cambiando)
        if ($request->has('tipo_habitacion_id') || $request->has('acomodacion_id') || $request->has('hotel_id')) {
            $existente = HabitacionHotel::where('hotel_id', $hotelId)
                ->where('tipo_habitacion_id', $tipoHabitacionId)
                ->where('acomodacion_id', $acomodacionId)
                ->where('id', '!=', $id)
                ->first();
                
            if ($existente) {
                return response()->json([
                    'message' => 'Ya existe esta combinación de tipo de habitación y acomodación para este hotel'
                ], 422);
            }
        }

        $habitacion->update($request->all());
        return response()->json(['data' => $habitacion, 'message' => 'Habitación actualizada con éxito'], 200);
    }

    public function destroy($id)
    {
        $habitacion = HabitacionHotel::find($id);
        
        if (!$habitacion) {
            return response()->json(['message' => 'Habitación no encontrada'], 404);
        }
        
        $habitacion->delete();
        return response()->json(['message' => 'Habitación eliminada con éxito'], 200);
    }

    public function getByHotel($hotelId)
    {
        $hotel = Hotel::find($hotelId);
        
        if (!$hotel) {
            return response()->json(['message' => 'Hotel no encontrado'], 404);
        }
        
        $habitaciones = HabitacionHotel::with('tipoHabitacion', 'acomodacion')
            ->where('hotel_id', $hotelId)
            ->get();
            
        return response()->json(['data' => $habitaciones], 200);
    }

    private function validarTipoAcomodacion($tipoHabitacionId, $acomodacionId)
    {
        // Estándar: Sencilla o Doble
        if ($tipoHabitacionId == TipoHabitacion::ESTANDAR) {
            return in_array($acomodacionId, [Acomodacion::SENCILLA, Acomodacion::DOBLE]);
        }
        
        // Junior: Triple o Cuádruple
        if ($tipoHabitacionId == TipoHabitacion::JUNIOR) {
            return in_array($acomodacionId, [Acomodacion::TRIPLE, Acomodacion::CUADRUPLE]);
        }
        
        // Suite: Sencilla, Doble o Triple
        if ($tipoHabitacionId == TipoHabitacion::SUITE) {
            return in_array($acomodacionId, [Acomodacion::SENCILLA, Acomodacion::DOBLE, Acomodacion::TRIPLE]);
        }
        
        return false;
    }
}