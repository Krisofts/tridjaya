<?php

namespace Database\Seeders;

use App\Auth\Services\AuthorizationService;
use App\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'superadmin@tridjaya.com',
            ],
            [
                'name'     => 'Superadmin',
                'password' => Hash::make('password123'),
            ]
        );

        app(AuthorizationService::class)
            ->addGroup($user, 'superadmin');
    }
}