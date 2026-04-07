<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('media', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('external_id'); // El ID que viene de la API (Jikan, TMDB, etc.)
            $table->string('title');
            $table->enum('media_type', ['anime', 'manga', 'movie', 'series', 'game']);
            $table->string('cover_url')->nullable(); // URL de la portada
            $table->text('synopsis')->nullable();
            $table->json('extra_data')->nullable(); // Aquí guardaremos los detalles técnicos
            $table->timestamps(); // Crea las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
