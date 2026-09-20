<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salles', function (Blueprint $table) {
            $table->id();

            // Référence courte et stable de la salle
            // Exemple : SALLE-01, SIMU-02...
            $table->string('code')
                ->nullable()
                ->unique();

            $table->string('nom');

            // Nombre maximum de stagiaires/personnes
            $table->unsignedInteger('capacite')
                ->nullable();

            // Exemple : Bâtiment A - RDC
            $table->string('localisation')
                ->nullable();

            // Exemple : cours, informatique, simulateur...
            $table->string('type_salle')
                ->nullable();

            // Matériel disponible :
            // vidéoprojecteur, postes informatiques, simulateur...
            $table->text('equipements')
                ->nullable();

            $table->text('commentaire')
                ->nullable();

            $table->boolean('actif')
                ->default(true)
                ->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};