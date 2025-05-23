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
        Schema::create('medication_prescription', function (Blueprint $table) {
            $table->primary(['medication_id', 'prescription_id']); // Clave primaria compuesta
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();

            $table->string('dosage_on_prescription')->nullable(); // Dosis específica para este medicamento en esta receta
            $table->string('quantity_prescribed')->nullable(); // Cantidad prescrita para este medicamento en esta receta
            $table->text('instructions_on_prescription')->nullable(); // Instrucciones específicas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_prescription');
    }
};
