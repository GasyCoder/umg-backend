<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!DB::table('settings')->where('key', 'about_video_poster_id')->exists()) {
            DB::table('settings')->insert([
                'key' => 'about_video_poster_id',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'about_video_poster_id')->delete();
    }
};
