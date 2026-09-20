<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('besoin_session_stage')) {
            Schema::create('besoin_session_stage', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('besoin_formation_id')
                    ->constrained('besoin_formations')
                    ->cascadeOnDelete();
                $table->foreignId('session_stage_id')
                    ->constrained('session_stages')
                    ->cascadeOnDelete();
                $table->unsignedInteger('effectif_prevu')->default(0);
                $table->timestamps();
                $table->unique(
                    ['besoin_formation_id', 'session_stage_id'],
                    'besoin_session_unique'
                );
            });
        }

        DB::table('besoin_formations')
            ->whereNotNull('session_stage_id')
            ->orderBy('id')
            ->chunkById(200, function ($besoins): void {
                foreach ($besoins as $besoin) {
                    DB::table('besoin_session_stage')->updateOrInsert(
                        [
                            'besoin_formation_id' => $besoin->id,
                            'session_stage_id' => $besoin->session_stage_id,
                        ],
                        [
                            'effectif_prevu' => max(
                                0,
                                (int) ($besoin->nombre_stagiaires ?? 0)
                            ),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }, 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('besoin_session_stage');
    }
};
