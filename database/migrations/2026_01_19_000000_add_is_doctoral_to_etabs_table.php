<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('etabs')) {
            Schema::create('etabs', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('name');
                $table->unsignedSmallInteger('type_id');
                $table->string('sigle')->unique();
                $table->string('director')->nullable();
                $table->string('slogan')->nullable();
                $table->longText('about')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('is_doctoral')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
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

        Schema::dropIfExists('etabs');
    }
};
