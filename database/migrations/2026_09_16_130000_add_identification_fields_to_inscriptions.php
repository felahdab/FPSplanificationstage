<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inscriptions', 'matricule')) {
            Schema::table('inscriptions', function (Blueprint $table): void {
                $table
                    ->string('matricule', 100)
                    ->nullable()
                    ->after('identifiant_annuaire');
            });
        }

        if (! Schema::hasColumn('inscriptions', 'nid')) {
            Schema::table('inscriptions', function (Blueprint $table): void {
                $table
                    ->string('nid', 100)
                    ->nullable()
                    ->after('matricule');
            });
        }

        if (! Schema::hasColumn('inscriptions', 'brevet')) {
            Schema::table('inscriptions', function (Blueprint $table): void {
                $table
                    ->string('brevet', 20)
                    ->nullable()
                    ->after('nid');
            });
        }

        if (! Schema::hasColumn('inscriptions', 'specialite')) {
            Schema::table('inscriptions', function (Blueprint $table): void {
                $table
                    ->string('specialite', 255)
                    ->nullable()
                    ->after('brevet');
            });
        }
    }

    public function down(): void
    {
        foreach (
            [
                'specialite',
                'brevet',
                'nid',
                'matricule',
            ] as $column
        ) {
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
};
