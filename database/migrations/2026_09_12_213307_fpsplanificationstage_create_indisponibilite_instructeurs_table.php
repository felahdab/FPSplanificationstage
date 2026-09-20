<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indisponibilite_instructeurs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('instructeur_id')
                ->constrained('instructeurs')
                ->cascadeOnDelete();

            $table->date('date_debut');
            $table->time('heure_debut')->nullable();

            $table->date('date_fin');
            $table->time('heure_fin')->nullable();

            $table->boolean('journee_entiere')
                ->default(true);

            $table->string('motif')
                ->nullable();

            $table->text('commentaire')
                ->nullable();

            $table->boolean('actif')
                ->default(true);

            // Pour les imports incrémentaux Excel
            $table->string('import_match_key')
                ->nullable()
                ->index();

            $table->string('import_hash', 64)
                ->nullable()
                ->index();

            $table->timestamp('dernier_import_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'instructeur_id',
                'date_debut',
                'date_fin',
            ], 'indispo_instructeur_dates_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indisponibilite_instructeurs');
    }
};