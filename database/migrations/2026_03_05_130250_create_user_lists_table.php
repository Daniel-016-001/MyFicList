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
        Schema::create('user_lists', function (Blueprint $table) {
            $table->id();
            // Relación con el usuario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Relación con el contenido (media)
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            
            // Estado del contenido
            $table->enum('status', ['watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch']);
            
            // Progreso (capítulos leídos, horas jugadas, etc.)
            $table->integer('progress')->default(0);
            
            // La nota que el usuario le da (del 1 al 10)
            $table->tinyInteger('score')->nullable();
            
            $table->timestamps();

            // Evitar que un usuario tenga el mismo anime/peli dos veces en su lista
            $table->unique(['user_id', 'media_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lists');
    }
};
