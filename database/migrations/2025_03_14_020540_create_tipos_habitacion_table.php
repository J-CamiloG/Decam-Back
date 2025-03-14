<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_habitacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });
        
        // Insertar los tipos de habitación predefinidos
        DB::table('tipos_habitacion')->insert([
            ['nombre' => 'ESTANDAR', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'JUNIOR', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'SUITE', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_habitacion');
    }
};