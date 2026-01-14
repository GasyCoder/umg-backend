<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add stat_teachers if it doesn't exist
        if (!DB::table('settings')->where('key', 'stat_teachers')->exists()) {
            DB::table('settings')->insert([
                'key' => 'stat_teachers',
                'value' => '500',
                'type' => 'string',
                'group' => 'stats',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update default values for existing stats
        DB::table('settings')->where('key', 'stat_students')->update(['value' => '12000']);
        DB::table('settings')->where('key', 'stat_staff')->update(['value' => '200']);
        DB::table('settings')->where('key', 'stat_establishments')->update(['value' => '6']);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'stat_teachers')->delete();
    }
};
