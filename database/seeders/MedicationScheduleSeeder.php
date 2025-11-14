<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MedicationSchedule;
use Illuminate\Database\Seeder;

final class MedicationScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicationSchedule::factory(50)->create();
    }
}
