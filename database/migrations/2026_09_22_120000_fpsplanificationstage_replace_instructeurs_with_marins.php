<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('instructeurs')) {
            return;
        }

        $marinIds = [];

        foreach (DB::table('instructeurs')->orderBy('id')->get() as $instructeur) {
            $marinIds[$instructeur->id] =
                $this->findOrCreateMarin($instructeur);
        }

        $this->dropInstructorConstraints();

        foreach ($marinIds as $instructeurId => $marinId) {
            foreach ($this->tables() as $table) {
                DB::table($table)
                    ->where('instructeur_id', $instructeurId)
                    ->update(['instructeur_id' => $marinId]);
            }
        }

        $this->removeDuplicateAssignments(
            'instructeur_stage',
            ['instructeur_id', 'stage_id']
        );

        $this->removeDuplicateAssignments(
            'instructeur_session_stage',
            ['session_stage_id', 'instructeur_id']
        );

        $this->addMarinConstraints();

        Schema::drop('instructeurs');
    }

    public function down(): void
    {
        if (Schema::hasTable('instructeurs')) {
            return;
        }

        $this->createInstructeursTable();

        $instructeurIds = collect();

        foreach ($this->tables() as $table) {
            $instructeurIds->push(
                ...DB::table($table)
                    ->pluck('instructeur_id')
                    ->all()
            );
        }

        foreach ($instructeurIds->filter()->unique() as $marinId) {
            $marin = DB::table('rh_marins')->find($marinId);

            if (! $marin) {
                continue;
            }

            DB::table('instructeurs')->insert([
                'id' => $marin->id,
                'identifiant_interne' => $marin->matricule,
                'nom' => $marin->nom,
                'prenom' => $marin->prenom,
                'email' => $marin->email,
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->dropMarinConstraints();
        $this->addInstructorConstraints();
    }

    private function findOrCreateMarin(object $instructeur): int
    {
        $matricule = $instructeur->identifiant_interne
            ? mb_substr(trim($instructeur->identifiant_interne), 0, 20)
            : null;

        $query = DB::table('rh_marins');

        if ($matricule !== null) {
            $marinId = (clone $query)
                ->whereRaw(
                    'UPPER(TRIM(matricule)) = ?',
                    [mb_strtoupper($matricule)]
                )
                ->value('id');

            if ($marinId !== null) {
                return (int) $marinId;
            }
        }

        if ($instructeur->email !== null) {
            $marinId = (clone $query)
                ->whereRaw(
                    'LOWER(TRIM(email)) = ?',
                    [mb_strtolower(trim($instructeur->email))]
                )
                ->value('id');

            if ($marinId !== null) {
                return (int) $marinId;
            }
        }

        $marinId = (clone $query)
            ->whereRaw(
                'UPPER(TRIM(nom)) = ?',
                [mb_strtoupper(trim($instructeur->nom))]
            )
            ->whereRaw(
                'UPPER(TRIM(prenom)) = ?',
                [mb_strtoupper(trim($instructeur->prenom))]
            )
            ->value('id');

        if ($marinId !== null) {
            return (int) $marinId;
        }

        return (int) DB::table('rh_marins')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'nom' => $instructeur->nom,
            'prenom' => $instructeur->prenom,
            'matricule' => $matricule,
            'nid' => null,
            'email' => $instructeur->email,
            'data' => json_encode([
                'status' => 'pending_uuid_confirmation',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function dropInstructorConstraints(): void
    {
        Schema::table('instructeur_session_stage', function (Blueprint $table): void {
            $table->index(
                'session_stage_id',
                'instructeur_session_stage_session_temp_index'
            );
        });

        Schema::table('instructeur_stage', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
            $table->dropUnique('instructeur_stage_unique');
        });

        Schema::table('instructeur_session_stage', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
            $table->dropUnique('instructeur_session_stage_unique');
        });

        Schema::table('indisponibilite_instructeurs', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
        });
    }

    private function addMarinConstraints(): void
    {
        Schema::table('instructeur_stage', function (Blueprint $table): void {
            $table->unique(
                ['instructeur_id', 'stage_id'],
                'instructeur_stage_unique'
            );
            $table->foreign('instructeur_id')
                ->references('id')
                ->on('rh_marins')
                ->cascadeOnDelete();
        });

        Schema::table('instructeur_session_stage', function (Blueprint $table): void {
            $table->unique(
                ['session_stage_id', 'instructeur_id'],
                'instructeur_session_stage_unique'
            );
            $table->foreign('instructeur_id')
                ->references('id')
                ->on('rh_marins')
                ->cascadeOnDelete();
            $table->dropIndex(
                'instructeur_session_stage_session_temp_index'
            );
        });

        Schema::table('indisponibilite_instructeurs', function (Blueprint $table): void {
            $table->foreign('instructeur_id')
                ->references('id')
                ->on('rh_marins')
                ->cascadeOnDelete();
        });
    }

    private function dropMarinConstraints(): void
    {
        Schema::table('instructeur_stage', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
        });

        Schema::table('instructeur_session_stage', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
        });

        Schema::table('indisponibilite_instructeurs', function (Blueprint $table): void {
            $table->dropForeign(['instructeur_id']);
        });
    }

    private function addInstructorConstraints(): void
    {
        foreach ($this->tables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreign('instructeur_id')
                    ->references('id')
                    ->on('instructeurs')
                    ->cascadeOnDelete();
            });
        }
    }

    private function removeDuplicateAssignments(
        string $table,
        array $columns
    ): void {
        $seen = [];

        foreach (DB::table($table)->orderBy('id')->get() as $row) {
            $key = implode('|', array_map(
                fn (string $column): string => (string) $row->{$column},
                $columns
            ));

            if (isset($seen[$key])) {
                DB::table($table)->where('id', $row->id)->delete();

                continue;
            }

            $seen[$key] = true;
        }
    }

    private function createInstructeursTable(): void
    {
        Schema::create('instructeurs', function (Blueprint $table): void {
            $table->id();
            $table->string('identifiant_interne')->nullable()->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->nullable()->index();
            $table->boolean('actif')->default(true)->index();
            $table->foreignId('salle_preferentielle_id')
                ->nullable()
                ->constrained('salles')
                ->nullOnDelete();
            $table->string('salle_preferentielle')->nullable();
            $table->text('commentaire')->nullable();
            $table->string('import_match_key')->nullable()->index();
            $table->string('import_hash', 64)->nullable()->index();
            $table->timestamp('dernier_import_at')->nullable();
            $table->timestamps();
        });
    }

    private function tables(): array
    {
        return [
            'instructeur_stage',
            'instructeur_session_stage',
            'indisponibilite_instructeurs',
        ];
    }
};
