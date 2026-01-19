<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('etabs')) {
            return;
        }

        Schema::table('etabs', function (Blueprint $table) {
            if (!Schema::hasColumn('etabs', 'is_doctoral')) {
                $table->boolean('is_doctoral')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('etabs')) {
            return;
        }

        Schema::table('etabs', function (Blueprint $table) {
            if (Schema::hasColumn('etabs', 'is_doctoral')) {
                $table->dropColumn('is_doctoral');
            }
        });
    }
};
