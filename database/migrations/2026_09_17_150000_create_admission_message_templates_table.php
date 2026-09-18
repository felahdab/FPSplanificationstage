<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admission_message_templates')) {
            return;
        }

        Schema::create('admission_message_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->nullOnDelete();
            $table->string('nom', 150);
            $table->string('objet', 500)->nullable();
            $table->longText('corps');
            $table->text('format_admis');
            $table->text('format_refuse');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['stage_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_message_templates');
    }
};
