<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'inscriptions',
            function (Blueprint $table): void {
                $table
                    ->foreignId(
                        'candidat_user_id'
                    )
                    ->nullable()
                    ->after('stagiaire_id')
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->string('candidat_nom')
                    ->nullable();

                $table
                    ->string('candidat_prenom')
                    ->nullable();

                $table
                    ->string('candidat_email')
                    ->nullable()
                    ->index();

                $table
                    ->string('candidat_matricule', 20)
                    ->nullable();

                $table
                    ->string('candidat_nid', 15)
                    ->nullable();

                $table
                    ->string('candidat_grade')
                    ->nullable();

                $table
                    ->string('candidat_brevet')
                    ->nullable();

                $table
                    ->string('candidat_specialite')
                    ->nullable();

                $table
                    ->string('candidat_unite')
                    ->nullable();

                $table
                    ->string('candidat_telephone')
                    ->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'inscriptions',
            function (Blueprint $table): void {
                $table->dropForeign([
                    'candidat_user_id',
                ]);

                $table->dropColumn([
                    'candidat_user_id',
                    'candidat_nom',
                    'candidat_prenom',
                    'candidat_email',
                    'candidat_matricule',
                    'candidat_nid',
                    'candidat_grade',
                    'candidat_brevet',
                    'candidat_specialite',
                    'candidat_unite',
                    'candidat_telephone',
                ]);
            }
        );
    }
};
