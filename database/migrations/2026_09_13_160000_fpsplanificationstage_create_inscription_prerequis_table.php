<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'inscription_prerequis',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('inscription_id')
                    ->constrained('inscriptions')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('prerequis_stage_id')
                    ->constrained('prerequis_stages')
                    ->cascadeOnDelete();

                $table
                    ->boolean('respecte')
                    ->default(false);

                $table->timestamps();

                $table->unique([
                    'inscription_id',
                    'prerequis_stage_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inscription_prerequis'
        );
    }
};