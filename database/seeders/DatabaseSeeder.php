<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            BranchSeeder::class,

            CrmPipelineSeeder::class,
            CrmPipelineStageSeeder::class,
            CrmSourceSeeder::class,
            CrmLostReasonSeeder::class,

            CrmActivityTypeSeeder::class,
            CrmActivityResultSeeder::class,
        ]);
    }
}