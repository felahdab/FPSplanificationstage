<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table): void {
            if (! Schema::hasColumn('stages', 'fif_generation')) {
                $table->string('fif_generation', 30)->nullable();
            }

            if (! Schema::hasColumn('stages', 'intitule_formation')) {
                $table->text('intitule_formation')->nullable();
            }

            if (! Schema::hasColumn('stages', 'service_emetteur')) {
                $table->string('service_emetteur')->nullable();
            }

            if (! Schema::hasColumn('stages', 'si_enregistrement_qualification')) {
                $table->text('si_enregistrement_qualification')->nullable();
            }

            if (! Schema::hasColumn('stages', 'echelle_grades')) {
                $table->text('echelle_grades')->nullable();
            }

            if (! Schema::hasColumn('stages', 'niveau_brevet')) {
                $table->text('niveau_brevet')->nullable();
            }

            if (! Schema::hasColumn('stages', 'lieux_formation')) {
                $table->text('lieux_formation')->nullable();
            }

            if (! Schema::hasColumn('stages', 'fonctions_visees')) {
                $table->text('fonctions_visees')->nullable();
            }

            if (! Schema::hasColumn('stages', 'objectif_formation')) {
                $table->longText('objectif_formation')->nullable();
            }

            if (! Schema::hasColumn('stages', 'domaines_competences_vises')) {
                $table->longText('domaines_competences_vises')->nullable();
            }

            if (! Schema::hasColumn('stages', 'criteres_certification')) {
                $table->longText('criteres_certification')->nullable();
            }

            if (! Schema::hasColumn('stages', 'evaluation_diagnostique')) {
                $table->text('evaluation_diagnostique')->nullable();
            }

            if (! Schema::hasColumn('stages', 'evaluation_formative')) {
                $table->text('evaluation_formative')->nullable();
            }

            if (! Schema::hasColumn('stages', 'evaluation_certificative')) {
                $table->text('evaluation_certificative')->nullable();
            }

            if (! Schema::hasColumn('stages', 'evaluation_format')) {
                $table->text('evaluation_format')->nullable();
            }

            if (! Schema::hasColumn('stages', 'pedagogie_groupes')) {
                $table->text('pedagogie_groupes')->nullable();
            }

            if (! Schema::hasColumn('stages', 'pedagogie_visite')) {
                $table->text('pedagogie_visite')->nullable();
            }

            if (! Schema::hasColumn('stages', 'pedagogie_video')) {
                $table->text('pedagogie_video')->nullable();
            }

            if (! Schema::hasColumn('stages', 'pedagogie_tableau_interactif')) {
                $table->text('pedagogie_tableau_interactif')->nullable();
            }

            if (! Schema::hasColumn('stages', 'pedagogie_autre')) {
                $table->longText('pedagogie_autre')->nullable();
            }

            if (! Schema::hasColumn('stages', 'fif_validation')) {
                $table->json('fif_validation')->nullable();
            }

            if (! Schema::hasColumn('stages', 'fif_donnees_source')) {
                $table->json('fif_donnees_source')->nullable();
            }

            if (! Schema::hasColumn('stages', 'fif_source_fichier')) {
                $table->string('fif_source_fichier')->nullable();
            }

            if (! Schema::hasColumn('stages', 'fif_import_hash')) {
                $table->string('fif_import_hash', 64)->nullable()->index();
            }

            if (! Schema::hasColumn('stages', 'fif_imported_at')) {
                $table->timestamp('fif_imported_at')->nullable();
            }
        });

        if (! Schema::hasTable('stage_fif_modules')) {
            Schema::create('stage_fif_modules', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('stage_id')
                    ->constrained('stages')
                    ->cascadeOnDelete();
                $table->unsignedInteger('ordre')->default(1);
                $table->text('module')->nullable();
                $table->longText('objectifs_competences')->nullable();
                $table->string('source')->nullable();
                $table->timestamps();

                $table->index(['stage_id', 'ordre']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_fif_modules');

        Schema::table('stages', function (Blueprint $table): void {
            $columns = [
                'fif_generation',
                'intitule_formation',
                'service_emetteur',
                'si_enregistrement_qualification',
                'echelle_grades',
                'niveau_brevet',
                'lieux_formation',
                'fonctions_visees',
                'objectif_formation',
                'domaines_competences_vises',
                'criteres_certification',
                'evaluation_diagnostique',
                'evaluation_formative',
                'evaluation_certificative',
                'evaluation_format',
                'pedagogie_groupes',
                'pedagogie_visite',
                'pedagogie_video',
                'pedagogie_tableau_interactif',
                'pedagogie_autre',
                'fif_validation',
                'fif_donnees_source',
                'fif_source_fichier',
                'fif_import_hash',
                'fif_imported_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('stages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
