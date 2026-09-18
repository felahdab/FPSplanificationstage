<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_stages', function (Blueprint $table) {
            $table->id();

            // Référence interne stable de la session.
            // Exemple : SES-000001
            $table->string('code_session')
                ->nullable()
                ->unique();

            $table->foreignId('stage_id')
                ->constrained('stages')
                ->cascadeOnDelete();

            // Salle réellement affectée à cette session.
            // Elle peut être différente de la salle préférentielle.
            $table->foreignId('salle_id')
                ->nullable()
                ->constrained('salles')
                ->nullOnDelete();

            // Début et fin réels de la session.
            $table->dateTime('debut');
            $table->dateTime('fin');

            // Capacité conservée au niveau de la session.
            // Cela permet de ne pas dépendre d'une future modification
            // du catalogue du stage.
            $table->unsignedInteger('capacite_min')
                ->nullable();

            $table->unsignedInteger('capacite_max')
                ->nullable();

            // brouillon / planifiee / confirmee / annulee / terminee
            $table->string('statut')
                ->default('brouillon')
                ->index();

            // Vrai lorsqu'un administrateur impose volontairement
            // une salle malgré la préférence automatique.
            $table->boolean('salle_forcee')
                ->default(false);

            // Permet plus tard de différencier une session créée
            // manuellement d'une session générée par le moteur.
            $table->string('source')
                ->default('manuel')
                ->index();

            $table->text('commentaire')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['debut', 'fin'],
                'session_stages_periode_index'
            );

            $table->index(
                ['salle_id', 'debut', 'fin'],
                'session_stages_salle_periode_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_stages');
    }
};