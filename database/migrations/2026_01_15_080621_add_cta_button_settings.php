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
        $settings = [
            ['key' => 'header_cta_text', 'value' => 'Candidater/Résultats/Inscription', 'type' => 'string', 'group' => 'header'],
            ['key' => 'header_cta_url', 'value' => '#', 'type' => 'string', 'group' => 'header'],
        ];

        foreach ($settings as $setting) {
            \DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->whereIn('key', [
            'header_cta_text',
            'header_cta_url',
        ])->delete();
    }
};
