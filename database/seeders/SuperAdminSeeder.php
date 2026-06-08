<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Auth\Services\AuthGroupService;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    { 
        $user = User::updateOrCreate(
            [
                'email' => 'superadmin@tridjaya.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),

                // sesuai model kamu
                'branch_id' => null,

                'force_password_change' => false,
                'password_changed_at' => now(),
            ]
        );

        // assign group superadmin
        app(AuthGroupService::class)->syncGroups(
            $user->id,
            'superadmin'
        );

        $this->command->info('Super Admin created successfully');
    }
}