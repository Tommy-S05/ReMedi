<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ResourceShare;
use Illuminate\Database\Seeder;

final class ResourceShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResourceShare::factory(5)->create();
    }
}
