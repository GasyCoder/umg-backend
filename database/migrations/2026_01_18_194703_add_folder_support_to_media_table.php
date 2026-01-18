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
        Schema::table('media', function (Blueprint $table) {
            $table->string('name')->after('path');
            $table->string('type')->default('file')->after('name');
            $table->foreignId('parent_id')->nullable()->after('created_by')->constrained('media')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['name', 'type', 'parent_id']);
        });
    }
};
