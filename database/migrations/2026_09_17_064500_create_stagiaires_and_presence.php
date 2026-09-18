<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stagiaires')) {
            Schema::create(
                'stagiaires',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('nom');
                    $table->string('prenom');

                    $table->string('grade')
                        ->nullable();

                    $table->string('brevet', 20)
                        ->nullable();

                    $table->string('specialite')
                        ->nullable();

                    $table->string('nid', 100)
                        ->nullable()
                        ->index();

                    $table->string('matricule', 100)
                        ->nullable()
                        ->index();

                    $table->string('unite')
                        ->nullable();

                    $table->string('email')
                        ->nullable();

                    $table->string('telephone')
                        ->nullable();

                    $table->timestamps();

                    $table->index([
                        'nom',
                        'prenom',
                    ]);
                }
            );
        }

        Schema::table(
            'inscriptions',
            function (Blueprint $table): void {
                if (
                    ! Schema::hasColumn(
                        'inscriptions',
                        'stagiaire_id'
                    )
                ) {
                    $table
                        ->foreignId('stagiaire_id')
                        ->nullable()
                        ->after('session_stage_id')
                        ->constrained('stagiaires')
                        ->nullOnDelete();
                }

                if (
                    ! Schema::hasColumn(
                        'inscriptions',
                        'presence'
                    )
                ) {
                    $table
                        ->string(
                            'presence',
                            20
                        )
                        ->default(
                            'non_renseignee'
                        )
                        ->after('statut')
                        ->index();
                }
            }
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('inscriptions')) {
            Schema::table(
                'inscriptions',
                function (Blueprint $table): void {
                    if (
                        Schema::hasColumn(
                            'inscriptions',
                            'stagiaire_id'
                        )
                    ) {
                        $table->dropForeign([
                            'stagiaire_id',
                        ]);

                        $table->dropColumn(
                            'stagiaire_id'
                        );
                    }

                    if (
                        Schema::hasColumn(
                            'inscriptions',
                            'presence'
                        )
                    ) {
                        $table->dropColumn(
                            'presence'
                        );
                    }
                }
            );
        }

        Schema::dropIfExists(
            'stagiaires'
        );
    }
};
