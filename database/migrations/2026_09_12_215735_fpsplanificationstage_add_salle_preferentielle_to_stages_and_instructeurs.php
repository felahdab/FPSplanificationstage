<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->foreignId('salle_preferentielle_id')
                ->nullable()
                ->after('capacite_min')
                ->constrained('salles')
                ->nullOnDelete();
        });

        Schema::table('instructeurs', function (Blueprint $table) {
            $table->foreignId('salle_preferentielle_id')
                ->nullable()
                ->after('actif')
                ->constrained('salles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'salle_preferentielle_id'
            );
        });

        Schema::table('instructeurs', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'salle_preferentielle_id'
            );
        });
    }
};