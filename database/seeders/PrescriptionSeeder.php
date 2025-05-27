<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::all()->each(function ($user) {
            // Crear algunas prescripciones para el usuario
            \App\Models\Prescription::factory(3)->create(['user_id' => $user->id])
                ->each(function ($prescription) use ($user) {
                    // Obtener algunas medicaciones aleatorias del mismo usuario para asociar
                    $medications = $user->medications()->inRandomOrder()->take(rand(1, 3))->get();

                    if ($medications->isNotEmpty()) {
                        $syncData = [];
                        foreach ($medications as $medication) {
                            $syncData[$medication->id] = [
                                'dosage_on_prescription' => $medication->dosage ?? '1 tablet',
                                'quantity_prescribed' => rand(10, 30) . ' units',
                                'instructions_on_prescription' => 'Take as directed by doctor for this prescription.',
                            ];
                        }
                        $prescription->medications()->sync($syncData);
                    }
                });
        });
    }
}
