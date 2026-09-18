<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable(
                'salle_occupations'
            )
        ) {
            return;
        }

        Schema::create(
            'salle_occupations',
            function (
                Blueprint $table
            ): void {
                $table->id();

                $table
                    ->foreignId(
                        'salle_id'
                    )
                    ->constrained(
                        'salles'
                    )
                    ->cascadeOnDelete();

                $table->string(
                    'libelle'
                );

                $table->dateTime(
                    'debut'
                );

                $table->dateTime(
                    'fin'
                );

                $table
                    ->string(
                        'source'
                    )
                    ->default(
                        'excel_sharepoint'
                    );

                $table
                    ->string(
                        'source_fichier'
                    )
                    ->nullable();

                $table
                    ->string(
                        'excel_sheet'
                    )
                    ->nullable();

                $table
                    ->string(
                        'excel_range'
                    )
                    ->nullable();

                $table
                    ->string(
                        'import_key',
                        64
                    )
                    ->unique();

                $table
                    ->timestamp(
                        'imported_at'
                    )
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'salle_id',
                    'debut',
                    'fin',
                ]);

                $table->index(
                    'source'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'salle_occupations'
        );
    }
};
