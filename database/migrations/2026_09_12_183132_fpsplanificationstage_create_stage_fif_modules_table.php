<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_fif_modules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->unsignedInteger('ordre')->default(1);
            $table->text('module')->nullable();
            $table->longText('objectifs_competences')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
            $table->index(['stage_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_fif_modules');
    }
};
