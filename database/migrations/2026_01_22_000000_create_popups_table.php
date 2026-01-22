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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            // Contenu principal
            $table->string('title');
            $table->text('content_html')->nullable();
            $table->string('button_text')->default('J\'ai compris');
            $table->string('button_url')->nullable();

            // Image/Icône
            $table->foreignId('image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('icon')->nullable(); // lucide icon name (ex: "bell", "graduation-cap")
            $table->string('icon_color')->nullable(); // couleur hex ou classe tailwind

            // Éléments de liste (JSON: [{icon, icon_color, title, description}])
            $table->json('items')->nullable();

            // Configuration d'affichage
            $table->integer('delay_ms')->default(0); // délai avant affichage (0 = immédiat)
            $table->boolean('show_on_all_pages')->default(true);
            $table->json('target_pages')->nullable(); // pages spécifiques ["/", "/actualites"]

            // Période d'affichage
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            // Statut et ordre
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0); // priorité si plusieurs popups actifs

            // Métadonnées
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Index
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
