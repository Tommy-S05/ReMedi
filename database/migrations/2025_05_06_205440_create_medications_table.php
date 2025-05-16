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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable(); // Tipo de medicamento (ej. Pastilla, Jarabe, Inyección)
            $table->string('dosage')->nullable(); // Dosis (ej. 10mg, 1 comprimido, 5ml)
            $table->text('instructions')->nullable(); // Instrucciones adicionales (ej. Tomar con comida)
            $table->integer('quantity')->nullable(); // Cantidad total del medicamento (opcional, para inventario)
            $table->string('strength')->nullable(); // Concentración (ej. 250 mg/5ml)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
