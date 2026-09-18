<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('besoin_formations', function (Blueprint $table) {
            $table->id();

            // Référence interne générée automatiquement.
            // Exemple : BES-000001
            $table->string('code_besoin')
                ->nullable()
                ->unique();

            $table->foreignId('stage_id')
                ->constrained('stages')
                ->cascadeOnDelete();

            // Bâtiment, unité ou organisme demandeur.
            $table->string('demandeur');

            /*
             * dates_fixes :
             * le stage doit avoir lieu exactement
             * entre les deux dates indiquées.
             *
             * plage :
             * le moteur pourra chercher un créneau
             * disponible dans cette période.
             */
            $table->string('type_periode')
                ->default('dates_fixes')
                ->index();

            $table->date('date_debut_souhaitee');

            $table->date('date_fin_souhaitee');

            /*
             * normale / haute / urgente
             */
            $table->string('priorite')
                ->default('normale')
                ->index();

            $table->unsignedInteger('nombre_stagiaires')
                ->nullable();

            /*
             * a_planifier
             * planifie
             * conflit
             * annule
             */
            $table->string('statut')
                ->default('a_planifier')
                ->index();

            /*
             * Session réellement créée à partir
             * de ce besoin.
             */
            $table->foreignId('session_stage_id')
                ->nullable()
                ->constrained('session_stages')
                ->nullOnDelete();

            $table->text('commentaire')
                ->nullable();

            /*
             * manuel / excel
             */
            $table->string('source')
                ->default('manuel')
                ->index();

            /*
             * Champs utilisés pour l'import Excel
             * incrémental.
             */
            $table->string('import_match_key')
                ->nullable()
                ->index();

            $table->string('import_hash', 64)
                ->nullable()
                ->index();

            $table->timestamp('dernier_import_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                [
                    'stage_id',
                    'date_debut_souhaitee',
                    'date_fin_souhaitee',
                ],
                'besoin_formations_stage_dates_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('besoin_formations');
    }
};