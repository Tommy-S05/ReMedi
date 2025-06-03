<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::all()->each(function ($user) {
            \App\Models\Medication::factory(5)
                ->has(\App\Models\MedicationSchedule::factory()->count(2), 'schedules')
                ->create(['user_id' => $user->id]);
        });
    }
}
