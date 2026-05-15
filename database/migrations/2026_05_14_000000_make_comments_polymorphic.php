<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Migramos los comentarios existentes de media_id a polimórfico
            // Añadimos las columnas polimórficas (nullable para la migración inicial)
            $table->string('commentable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('commentable_id')->nullable()->after('commentable_type');

            // Índice compuesto para búsquedas rápidas
            $table->index(['commentable_type', 'commentable_id']);
        });

        // Migrar los registros existentes: todos apuntan a Media
        DB::table('comments')->whereNotNull('media_id')->update([
            'commentable_type' => 'App\\Models\\Media',
            'commentable_id' => DB::raw('media_id'),
        ]);

        Schema::table('comments', function (Blueprint $table) {
            // Ahora que ya está relleno, hacemos NOT NULL
            $table->string('commentable_type')->nullable(false)->change();
            $table->unsignedBigInteger('commentable_id')->nullable(false)->change();

            // Hacemos media_id nullable (ya no es la FK principal)
            $table->dropForeign(['media_id']);
            $table->unsignedBigInteger('media_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // 1. Eliminamos el índice y las columnas polimórficas
            $table->dropIndex(['commentable_type', 'commentable_id']);
            $table->dropColumn(['commentable_type', 'commentable_id']);

            // 2. Volvemos a hacer media_id obligatorio
            // Nota: Si tienes datos huérfanos, esto podría seguir fallando.
            // Lo ideal es limpiar los nulos antes:
            // DB::table('comments')->whereNull('media_id')->delete();

            $table->unsignedBigInteger('media_id')->nullable(false)->change();
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
        });
    }
};
