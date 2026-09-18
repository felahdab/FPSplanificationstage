<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'instructeur_session_stage',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('session_stage_id')
                    ->constrained('session_stages')
                    ->cascadeOnDelete();

                $table->foreignId('instructeur_id')
                    ->constrained('instructeurs')
                    ->cascadeOnDelete();

                // principal / suppleant / indifferent
                $table->string('role')
                    ->default('indifferent');

                $table->timestamps();

                $table->unique(
                    ['session_stage_id', 'instructeur_id'],
                    'instructeur_session_stage_unique'
                );

                $table->index(
                    ['instructeur_id', 'session_stage_id'],
                    'instructeur_session_stage_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'instructeur_session_stage'
        );
    }
};