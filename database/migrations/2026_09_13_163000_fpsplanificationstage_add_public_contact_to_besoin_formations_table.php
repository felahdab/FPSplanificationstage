<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'besoin_formations',
            function (Blueprint $table): void {
                $table
                    ->string('contact_nom')
                    ->nullable()
                    ->after('demandeur');

                $table
                    ->string('contact_email')
                    ->nullable()
                    ->after('contact_nom');

                $table
                    ->string('contact_telephone')
                    ->nullable()
                    ->after('contact_email');

                /*
                 * Jeton non prédictible utilisé
                 * pour les futures pages publiques
                 * de confirmation / suivi.
                 */
                $table
                    ->uuid('public_token')
                    ->nullable()
                    ->unique()
                    ->after('source');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'besoin_formations',
            function (Blueprint $table): void {
                $table->dropUnique([
                    'public_token',
                ]);

                $table->dropColumn([
                    'contact_nom',
                    'contact_email',
                    'contact_telephone',
                    'public_token',
                ]);
            }
        );
    }
};