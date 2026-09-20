<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructeurs', function (Blueprint $table) {
            $table->id();

            // Référence stable si elle existe dans le fichier d'import.
            $table->string('identifiant_interne')
                ->nullable()
                ->unique();

            $table->string('nom');
            $table->string('prenom');

            $table->string('email')
                ->nullable()
                ->index();

            $table->boolean('actif')
                ->default(true)
                ->index();

            $table->foreignId('salle_preferentielle_id')
                ->nullable()
                ->constrained('salles')
                ->nullOnDelete();

            $table->string('salle_preferentielle')
                ->nullable();

            $table->text('commentaire')
                ->nullable();

            // Utilisé par l'import incrémental Excel.
            $table->string('import_match_key')
                ->nullable()
                ->index();

            $table->string('import_hash', 64)
                ->nullable()
                ->index();

            $table->timestamp('dernier_import_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructeurs');
    }
};