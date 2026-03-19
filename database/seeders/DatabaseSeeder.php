<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('SEED_ADMIN_NAME', 'MAX Admin'),
            'password' => env('SEED_ADMIN_PASSWORD', 'ChangeMe1234!'),
            'role' => 'admin',
        ]);
    }
}
