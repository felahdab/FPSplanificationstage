<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasTable(
                'besoin_formations'
            )
            || ! Schema::hasColumn(
                'besoin_formations',
                'date_fin_souhaitee'
            )
        ) {
            return;
        }

        Schema::table(
            'besoin_formations',
            function (
                Blueprint $table
            ): void {
                $table
                    ->date(
                        'date_fin_souhaitee'
                    )
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        if (
            ! Schema::hasTable(
                'besoin_formations'
            )
            || ! Schema::hasColumn(
                'besoin_formations',
                'date_fin_souhaitee'
            )
        ) {
            return;
        }

        /*
         * Sécurité pour un éventuel rollback :
         * une demande "date de début imposée" sans date de fin
         * récupère sa date de début dans l'ancienne colonne obligatoire.
         */
        DB::table(
            'besoin_formations'
        )
            ->whereNull(
                'date_fin_souhaitee'
            )
            ->update([
                'date_fin_souhaitee' =>
                    DB::raw(
                        'date_debut_souhaitee'
                    ),
            ]);

        Schema::table(
            'besoin_formations',
            function (
                Blueprint $table
            ): void {
                $table
                    ->date(
                        'date_fin_souhaitee'
                    )
                    ->nullable(false)
                    ->change();
            }
        );
    }
};
