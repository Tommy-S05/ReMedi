<?php

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
        Schema::create('medication_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');

            // Hora de la toma. Para 'hourly_interval', es la hora de la primera dosis en start_date.
            // Hora específica del día para tomarlo (ej. 08:00:00)
            $table->time('time_to_take');

            // Tipos: 'daily', 'specific_days', 'interval_days', 'hourly_interval'
            $table->string('frequency_type');

            // Para frequency_type = 'specific_days' (ej. Lunes, Miércoles)
            // Almacena un array de números de día [0,1,2,3,4,5,6] para Dom,Lun,Mar...
            $table->json('days_of_week')->nullable();

            // Para frequency_type = 'interval_in_days' (ej. cada 3 días)
            $table->integer('interval_in_days')->nullable();

            // Para frequency_type = 'hourly_interval' (ej. cada 8 horas)
            $table->integer('interval_in_hours')->nullable();

            $table->date('start_date'); // Fecha de inicio del tratamiento/horario
            $table->date('end_date')->nullable(); // Fecha de fin del tratamiento/horario (opcional)
            $table->boolean('is_active')->default(true); // Para activar/desactivar un horario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_schedules');
    }
};
