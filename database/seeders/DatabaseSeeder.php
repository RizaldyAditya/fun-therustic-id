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
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@therustic.id',
            'password' => '$2y$12$GPpc46oxBf2IEwSbGEuSo.6SsUzvUU.JP0.lvivIbL7ZqXbQxUy4y' // password = 123456
        ]);

        $this->call([
            StatusSeeder::class,
            SourceSeeder::class
        ]);
    }
}
