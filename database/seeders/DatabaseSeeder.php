<?php

namespace Database\Seeders;

use App\Models\User;
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
        $user = User::factory()->create([
            'name' => 'Web Master',
            'email' => 'webmaster@therustic.id',
            'password' => '$2y$12$GPpc46oxBf2IEwSbGEuSo.6SsUzvUU.JP0.lvivIbL7ZqXbQxUy4y' // password = 123456
        ]);
        $user->assignRole('super_admin');

        $this->call([
            StatusSeeder::class,
            SourceSeeder::class
        ]);
    }
}
