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
            if (!Schema::hasColumn('etablissements', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }

            if (!Schema::hasColumn('etablissements', 'type_id')) {
                $table->unsignedSmallInteger('type_id')->nullable()->after('name');
            }

            if (!Schema::hasColumn('etablissements', 'sigle')) {
                $table->string('sigle')->nullable()->unique()->after('type_id');
            }

            if (!Schema::hasColumn('etablissements', 'director')) {
                $table->string('director')->nullable()->after('sigle');
            }

            if (!Schema::hasColumn('etablissements', 'slogan')) {
                $table->string('slogan')->nullable()->after('director');
            }

            if (!Schema::hasColumn('etablissements', 'about')) {
                $table->longText('about')->nullable()->after('slogan');
            }

            if (!Schema::hasColumn('etablissements', 'image_path')) {
                $table->string('image_path')->nullable()->after('about');
            }

            if (!Schema::hasColumn('etablissements', 'status')) {
                $table->boolean('status')->default(true)->after('image_path');
            }

            if (!Schema::hasColumn('etablissements', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('etablissements')) {
            return;
        }

        Schema::table('etablissements', function (Blueprint $table) {
            if (Schema::hasColumn('etablissements', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('etablissements', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('etablissements', 'image_path')) {
                $table->dropColumn('image_path');
            }

            if (Schema::hasColumn('etablissements', 'about')) {
                $table->dropColumn('about');
            }

            if (Schema::hasColumn('etablissements', 'slogan')) {
                $table->dropColumn('slogan');
            }

            if (Schema::hasColumn('etablissements', 'director')) {
                $table->dropColumn('director');
            }

            if (Schema::hasColumn('etablissements', 'sigle')) {
                $table->dropColumn('sigle');
            }

            if (Schema::hasColumn('etablissements', 'type_id')) {
                $table->dropColumn('type_id');
            }

            if (Schema::hasColumn('etablissements', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
