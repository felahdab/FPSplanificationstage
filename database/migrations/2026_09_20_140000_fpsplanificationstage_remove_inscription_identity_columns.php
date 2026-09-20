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
            Schema::hasColumn('inscriptions', 'stagiaire_id')
            && Schema::hasTable('rh_marins')
        ) {
            DB::table('inscriptions')
                ->whereNull('stagiaire_id')
                ->get()
                ->each(function (object $inscription): void {
                    $marin = null;

                    foreach (['nid', 'matricule'] as $identifier) {
                        if (
                            ! Schema::hasColumn('inscriptions', $identifier)
                            || blank($inscription->{$identifier})
                        ) {
                            continue;
                        }

                        $marin = DB::table('rh_marins')
                            ->whereRaw(
                                'UPPER(TRIM(' . $identifier . ')) = ?',
                                [mb_strtoupper(trim($inscription->{$identifier}))]
                            )
                            ->first();

                        if ($marin) {
                            break;
                        }
                    }

                    if (! $marin && isset($inscription->nom, $inscription->prenom)) {
                        $marin = DB::table('rh_marins')
                            ->whereRaw(
                                'UPPER(TRIM(nom)) = ?',
                                [mb_strtoupper(trim($inscription->nom))]
                            )
                            ->whereRaw(
                                'UPPER(TRIM(prenom)) = ?',
                                [mb_strtoupper(trim($inscription->prenom))]
                            )
                            ->first();
                    }

                    if (! $marin && isset($inscription->nom, $inscription->prenom)) {
                        $marinId = DB::table('rh_marins')->insertGetId([
                            'nom' => $inscription->nom,
                            'prenom' => $inscription->prenom,
                            'nid' => $inscription->nid ?? null,
                            'matricule' => $inscription->matricule ?? null,
                            'email' => $inscription->email ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $marinId = $marin?->id;
                    }

                    if ($marinId) {
                        DB::table('inscriptions')
                            ->where('id', $inscription->id)
                            ->update(['stagiaire_id' => $marinId]);
                    }
                });
        }

        $columns = [
            'identifiant_annuaire',
            'matricule',
            'nid',
            'brevet',
            'specialite',
            'nom',
            'prenom',
            'grade',
            'unite',
            'email',
            'telephone',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('inscriptions', $column)) {
                Schema::table(
                    'inscriptions',
                    function (Blueprint $table) use ($column): void {
                        $table->dropColumn($column);
                    }
                );
            }
        }
    }

    public function down(): void
    {
        Schema::table(
            'inscriptions',
            function (Blueprint $table): void {
                $table->string('identifiant_annuaire')->nullable()->index();
                $table->string('matricule', 100)->nullable()->index();
                $table->string('nid', 100)->nullable()->index();
                $table->string('brevet', 20)->nullable();
                $table->string('specialite')->nullable();
                $table->string('nom')->nullable();
                $table->string('prenom')->nullable();
                $table->string('grade')->nullable();
                $table->string('unite')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('telephone')->nullable();
            }
        );
    }
};
