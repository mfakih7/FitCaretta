<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@fitcaretta.com')],
            [
                'name' => config('store.name', 'FitCaretta') . ' Admin',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role' => UserRole::SUPER_ADMIN,
                'is_active' => true,
            ]
        );

        $this->call([
            FitCarettaDemoSeeder::class,
        ]);
    }
}
