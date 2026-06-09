<?php

namespace Database\Seeders;

use App\Models\User;
use App\Shared\Enums\UserRole;
use App\Shared\Enums\UserStatus;
use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer le superadmin par défaut
        User::updateOrCreate(
            ['email' => 'maitedjkbarry@icloud.com'],
            [
                'name' => 'Moustapha BARRY',
                'phone' => '67205736',
                'password' => Hash::make('superadmin123'),
                'role' => UserRole::SUPERADMIN->value,
                'status' => UserStatus::ACTIVE->value,
                'is_active' => true,
            ]
        );

        $this->call(PermissionSeeder::class);
    }
}
