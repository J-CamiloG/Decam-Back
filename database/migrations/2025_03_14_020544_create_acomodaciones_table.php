<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acomodaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });
        
        // Insertar las acomodaciones predefinidas
        DB::table('acomodaciones')->insert([
            ['nombre' => 'SENCILLA', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'DOBLE', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'TRIPLE', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CUÁDRUPLE', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('acomodaciones');
    }
};