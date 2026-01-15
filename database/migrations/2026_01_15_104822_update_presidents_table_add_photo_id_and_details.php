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
        Schema::table('presidents', function (Blueprint $table) {
            // Ajouter photo_id pour utiliser le système Media centralisé
            $table->foreignId('photo_id')->nullable()->after('photo_url')->constrained('media')->nullOnDelete();

            // Ajouter des champs supplémentaires
            $table->string('title')->nullable()->after('name'); // Ex: "Recteur", "Président"
            $table->text('bio')->nullable()->after('mandate_end'); // Courte biographie
            $table->boolean('is_current')->default(false)->after('bio'); // Président actuel

            // Supprimer photo_url car on utilise photo_id
            $table->dropColumn('photo_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presidents', function (Blueprint $table) {
            $table->string('photo_url')->nullable()->after('mandate_end');

            $table->dropForeign(['photo_id']);
            $table->dropColumn(['photo_id', 'title', 'bio', 'is_current']);
        });
    }
};
