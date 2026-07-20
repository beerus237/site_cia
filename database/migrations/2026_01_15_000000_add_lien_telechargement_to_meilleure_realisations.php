<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meilleure_realisations', function (Blueprint $table) {
            $table->string('lien_telechargement')->nullable()->after('lien');
        });
    }

    public function down(): void
    {
        Schema::table('meilleure_realisations', function (Blueprint $table) {
            $table->dropColumn('lien_telechargement');
        });
    }
};
