<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MedicationTakeLog;
use Illuminate\Database\Seeder;

final class MedicationTakeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicationTakeLog::factory(50)->create();
    }
}
