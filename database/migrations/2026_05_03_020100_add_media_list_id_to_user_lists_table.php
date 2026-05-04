<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_lists', function (Blueprint $table) {
            $table->foreignId('media_list_id')->nullable()->after('media_id')->constrained('media_lists')->nullOnDelete();
        });

        $users = DB::table('user_lists')->distinct('user_id')->pluck('user_id');

        foreach ($users as $userId) {
            $defaultListId = DB::table('media_lists')->insertGetId([
                'user_id' => $userId,
                'name' => 'Principal',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('user_lists')->where('user_id', $userId)->update(['media_list_id' => $defaultListId]);
        }

        Schema::table('user_lists', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'media_id']);
            $table->unique(['user_id', 'media_id', 'media_list_id']);
        });
    }

    public function down(): void
    {
        Schema::table('user_lists', function (Blueprint $table) {
            $table->dropForeign(['media_list_id']);
            $table->dropUnique(['user_id', 'media_id', 'media_list_id']);
            $table->unique(['user_id', 'media_id']);
            $table->dropColumn('media_list_id');
        });
    }
};
