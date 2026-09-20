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
                'session_stage_historiques'
            )
        ) {
            return;
        }

        Schema::create(
            'session_stage_historiques',
            function (
                Blueprint $table
            ): void {
                $table->id();

                $table
                    ->foreignId(
                        'session_stage_id'
                    )
                    ->constrained(
                        'session_stages'
                    )
                    ->cascadeOnDelete();

                $table->string(
                    'type',
                    32
                );

                $table
                    ->dateTime(
                        'ancien_debut'
                    )
                    ->nullable();

                $table
                    ->dateTime(
                        'ancien_fin'
                    )
                    ->nullable();

                $table
                    ->dateTime(
                        'nouveau_debut'
                    )
                    ->nullable();

                $table
                    ->dateTime(
                        'nouveau_fin'
                    )
                    ->nullable();

                $table
                    ->string(
                        'ancien_statut',
                        32
                    )
                    ->nullable();

                $table
                    ->string(
                        'nouveau_statut',
                        32
                    )
                    ->nullable();

                $table
                    ->string(
                        'motif'
                    )
                    ->nullable();

                $table
                    ->text(
                        'commentaire'
                    )
                    ->nullable();

                $table
                    ->json(
                        'meta'
                    )
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'session_stage_id',
                    'type',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'session_stage_historiques'
        );
    }
};
