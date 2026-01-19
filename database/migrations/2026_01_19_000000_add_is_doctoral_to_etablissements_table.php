<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('etablissements')) {
            return;
        }

        Schema::table('etablissements', function (Blueprint $table) {
            if (!Schema::hasColumn('etablissements', 'is_doctoral')) {
                $table->boolean('is_doctoral')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('etablissements')) {
            return;
        }

        Schema::table('etablissements', function (Blueprint $table) {
            if (Schema::hasColumn('etablissements', 'is_doctoral')) {
                $table->dropColumn('is_doctoral');
            }
        });
    }
};
