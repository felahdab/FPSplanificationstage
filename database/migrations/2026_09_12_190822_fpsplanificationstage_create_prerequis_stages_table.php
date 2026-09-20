<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prerequis_stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stage_id')
                ->constrained('stages')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('ordre')->default(0);

            $table->text('libelle');

            $table->boolean('obligatoire')->default(true);

            $table->boolean('actif')->default(true);
            $table->string('source')->default('manuel')->index();
            $table->string('source_colonne')->nullable();

            $table->timestamps();

            $table->index(['stage_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prerequis_stages');
    }
};