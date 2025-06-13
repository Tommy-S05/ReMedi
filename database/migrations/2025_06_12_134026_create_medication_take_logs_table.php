<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medication_take_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->timestamp('scheduled_for'); // La hora exacta para la que estaba programado el recordatorio (en UTC)
            $table->timestamp('action_taken_at')->nullable(); // La hora en que el usuario interactuó (marcó como tomado/omitido)
            $table->text('notes')->nullable(); // Notas opcionales del usuario para esta toma
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_take_logs');
    }
};
