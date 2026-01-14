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
        Schema::table('slides', function (Blueprint $table) {
            $table->string('bg_color_light')->default('bg-blue-900')->after('category_id');
            $table->string('bg_color_dark')->default('bg-slate-800')->after('bg_color_light');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn(['bg_color_light', 'bg_color_dark']);
        });
    }
};
