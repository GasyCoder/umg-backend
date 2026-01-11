<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $stats = [
            ['key' => 'stat_students', 'value' => '15000', 'type' => 'string', 'group' => 'stats'],
            ['key' => 'stat_staff', 'value' => '500', 'type' => 'string', 'group' => 'stats'],
            ['key' => 'stat_formations', 'value' => '50', 'type' => 'string', 'group' => 'stats'],
            ['key' => 'stat_establishments', 'value' => '8', 'type' => 'string', 'group' => 'stats'],
            ['key' => 'stat_services', 'value' => '30', 'type' => 'string', 'group' => 'stats'],
        ];

        foreach ($stats as $stat) {
            // Only insert if not exists
            if (!DB::table('settings')->where('key', $stat['key'])->exists()) {
                DB::table('settings')->insert(array_merge($stat, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'stat_students',
            'stat_staff',
            'stat_formations',
            'stat_establishments',
            'stat_services',
        ])->delete();
    }
};
