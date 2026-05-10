<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_lists', function (Blueprint $table) {
            $table->foreignId('media_list_id')->nullable()->after('media_id')->constrained('media_lists')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_lists', function (Blueprint $table) {
            $table->dropForeign(['media_list_id']);
            $table->dropColumn('media_list_id');
        });
    }
};
