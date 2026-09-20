<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();

            // Identifiant interne
            $table->string('code_stage')->nullable()->unique();

            // Références catalogue
            $table->string('numero_externe')->nullable()->index();
            $table->date('date_creation_catalogue')->nullable();
            $table->date('date_maj_catalogue')->nullable();
            $table->text('nature_maj')->nullable();

            // Informations générales
            $table->string('raf')->nullable();
            $table->string('centre_formation')->nullable();
            $table->string('typologie')->nullable();

            $table->string('libelle_court');
            $table->string('appellation_chorus')->nullable();
            $table->string('branche')->nullable();
            $table->string('adc')->nullable();

            $table->text('libelle_long')->nullable();
            $table->text('diplomes_qualifications')->nullable();
            $table->string('unite_certification')->nullable();

            $table->string('cursus_ouvert')->nullable();
            $table->string('sirh')->nullable();
            $table->string('ouverture_licence')->nullable();

            $table->date('date_cdf')->nullable();
            $table->string('ecole_pilote_cdf')->nullable();

            // Organisation
            $table->decimal('duree_jours', 4, 1)->nullable();
            $table->unsignedSmallInteger('nb_sessions_annuelles')->nullable();

            $table->unsignedSmallInteger('capacite_max')->nullable();
            $table->unsignedSmallInteger('capacite_min')->nullable();
            $table->foreignId('salle_preferentielle_id')
                ->nullable()
                ->constrained('salles')
                ->nullOnDelete();

            // Population
            $table->boolean('ouvert_off')->default(false);
            $table->boolean('ouvert_om')->default(false);
            $table->boolean('ouvert_qmm_mo')->default(false);

            $table->boolean('ouverture_etrangers')->nullable();
            $table->boolean('possibilite_ead')->nullable();
            $table->decimal('duree_ead_ui', 5, 1)->nullable();

            $table->boolean('ouverture_vca')->nullable();
            $table->boolean('ouverture_vae')->nullable();

            $table->text('autres_beneficiaires')->nullable();
            $table->text('observations')->nullable();

            // Gestion
            $table->boolean('actif')->default(true);

            // Import incrémental
            $table->string('catalogue_match_key')->nullable()->index();
            $table->string('catalogue_hash', 64)->nullable()->index();
            $table->timestamp('dernier_import_at')->nullable();

            $table->string('fif_generation', 30)->nullable();
            $table->text('intitule_formation')->nullable();
            $table->string('service_emetteur')->nullable();
            $table->text('si_enregistrement_qualification')->nullable();
            $table->text('echelle_grades')->nullable();
            $table->text('niveau_brevet')->nullable();
            $table->text('lieux_formation')->nullable();
            $table->text('fonctions_visees')->nullable();
            $table->longText('objectif_formation')->nullable();
            $table->longText('domaines_competences_vises')->nullable();
            $table->longText('criteres_certification')->nullable();
            $table->text('evaluation_diagnostique')->nullable();
            $table->text('evaluation_formative')->nullable();
            $table->text('evaluation_certificative')->nullable();
            $table->text('evaluation_format')->nullable();
            $table->text('pedagogie_groupes')->nullable();
            $table->text('pedagogie_visite')->nullable();
            $table->text('pedagogie_video')->nullable();
            $table->text('pedagogie_tableau_interactif')->nullable();
            $table->longText('pedagogie_autre')->nullable();
            $table->json('fif_validation')->nullable();
            $table->json('fif_donnees_source')->nullable();
            $table->string('fif_source_fichier')->nullable();
            $table->string('fif_import_hash', 64)->nullable()->index();
            $table->timestamp('fif_imported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};