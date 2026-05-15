<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->integer('episodes_count')->nullable()->after('synopsis');
            $table->string('episode_duration')->nullable()->after('episodes_count');
            $table->integer('total_duration')->nullable()->after('episode_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['episodes_count', 'episode_duration', 'total_duration']);
        });
    }
};
