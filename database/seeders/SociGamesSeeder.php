<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SociGamesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vars')->updateOrInsert(
            ['name' => 'socigames_renpy_update'],
            [
                'value' => 'https://socigames.com/category/adult/renpy/',
                'group' => 'AVN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
