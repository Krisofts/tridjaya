<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['code' => 'D-01', 'name' => 'Pagaden'],
            ['code' => 'D-02', 'name' => 'Haurgeulis'],
            ['code' => 'D-03', 'name' => 'Soklat'],
            ['code' => 'D-04', 'name' => 'Patokbeusi'],
            ['code' => 'D-05', 'name' => 'Pamanukan'],
            ['code' => 'D-06', 'name' => 'Samrat'],
            ['code' => 'D-07', 'name' => 'Bahu'],
            ['code' => 'D-08', 'name' => 'Purwadadi'],
            ['code' => 'D-09', 'name' => 'Cimalaka'],
            ['code' => 'D-10', 'name' => 'Cikampek'],
            ['code' => 'D-11', 'name' => 'Pabuaran'],
            ['code' => 'D-12', 'name' => 'Cibaduyut'],
            ['code' => 'D-13', 'name' => 'Cilacap'],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                [
                    'name' => $branch['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}