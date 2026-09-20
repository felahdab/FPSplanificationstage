<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'inscriptions',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('code_inscription')
                    ->nullable()
                    ->unique();

                $table
                    ->foreignId('session_stage_id')
                    ->constrained('session_stages')
                    ->restrictOnDelete();

                $table
                    ->foreignId('stagiaire_id')
                    ->nullable()
                    ->constrained('rh_marins')
                    ->nullOnDelete();

                /*
                 * Workflow :
                 *
                 * attente_nemo
                 * confirmee
                 * attente_derogation
                 * liste_attente
                 * refusee
                 * annulee
                 */
                $table
                    ->string('statut')
                    ->default('attente_nemo')
                    ->index();

                $table
                    ->string('presence', 20)
                    ->default('non_renseignee')
                    ->index();

                /*
                 * Le NEMO en attente réserve
                 * déjà la place.
                 */
                $table
                    ->boolean('nemo_recu')
                    ->default(false);

                $table
                    ->timestamp('nemo_recu_at')
                    ->nullable();

                /*
                 * Gestion de la dérogation.
                 */
                $table
                    ->boolean(
                        'derogation_demandee'
                    )
                    ->default(false);

                $table
                    ->string(
                        'derogation_statut'
                    )
                    ->nullable();

                $table
                    ->text(
                        'derogation_motif'
                    )
                    ->nullable();

                $table
                    ->text('commentaire')
                    ->nullable();

                /*
                 * manuel : saisie administration
                 * public : inscription publique
                 * import : éventuel import futur
                 */
                $table
                    ->string('source')
                    ->default('manuel');

                $table->timestamps();

                $table->index([
                    'session_stage_id',
                    'statut',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inscriptions'
        );
    }
};