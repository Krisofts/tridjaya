<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            BranchSeeder::class,

            // Wilayah Indonesia
            WilayahSeeder::class,

            CrmLeadSourceSeeder::class,
            CrmPipelineSeeder::class,
            CrmPipelineStageSeeder::class,

            // Result Flow System
            CrmResultSeeder::class,
            CrmResultStageMappingSeeder::class,

            SuperAdminSeeder::class,
            CrmPipelineStageTaskSeeder::class,
        ]);
    }
}