<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagnes_collecte', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->text('description');
            // Types de contribution acceptés pour cette campagne, ex: ["image","texte","dataset"]
            $table->json('types_acceptes');
            $table->string('image_url')->nullable();
            // Lien optionnel vers un projet affiché sur "Nos réalisations"
            // (permet d'y afficher un bouton "Contribuer")
            $table->foreignId('projet_id')->nullable()->constrained('projets')->nullOnDelete();
            $table->boolean('est_active')->default(true);
            $table->timestamps();

            $table->index('est_active');
        });

        Schema::table('contributions', function (Blueprint $table) {
            $table->foreignId('campagne_id')->nullable()->after('user_id')
                ->constrained('campagnes_collecte')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campagne_id');
        });

        Schema::dropIfExists('campagnes_collecte');
    }
};
