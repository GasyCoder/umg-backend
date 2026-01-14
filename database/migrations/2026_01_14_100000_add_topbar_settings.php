<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $topbarSettings = [
            [
                'key' => 'topbar_library_label',
                'value' => 'Bibliothèque',
                'type' => 'string',
                'group' => 'topbar',
            ],
            [
                'key' => 'topbar_library_url',
                'value' => '#',
                'type' => 'string',
                'group' => 'topbar',
            ],
            [
                'key' => 'topbar_webmail_label',
                'value' => 'Webmail',
                'type' => 'string',
                'group' => 'topbar',
            ],
            [
                'key' => 'topbar_webmail_url',
                'value' => 'https://webmail.univ-mahajanga.mg',
                'type' => 'string',
                'group' => 'topbar',
            ],
            [
                'key' => 'topbar_digital_label',
                'value' => 'Espace Numérique',
                'type' => 'string',
                'group' => 'topbar',
            ],
            [
                'key' => 'topbar_digital_url',
                'value' => '#',
                'type' => 'string',
                'group' => 'topbar',
            ],
        ];

        foreach ($topbarSettings as $setting) {
            if (!DB::table('settings')->where('key', $setting['key'])->exists()) {
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'topbar')->delete();
    }
};
