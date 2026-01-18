<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_important')->default(false)->after('is_pinned');
            $table->unsignedBigInteger('views_count')->default(0)->after('is_important');
            $table->unsignedBigInteger('unique_views_count')->default(0)->after('views_count');

            $table->index(['is_important', 'published_at']);
            $table->index(['views_count']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['is_important', 'published_at']);
            $table->dropIndex(['views_count']);
            $table->dropColumn(['is_important', 'views_count', 'unique_views_count']);
        });
    }
};

