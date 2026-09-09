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

        $engines = [
            '3411' => "Ren'Py",
            '3413' => 'Unity',
            '3414' => 'RPGM',
            '3416' => 'Unreal Engine',
            '3418' => 'Java',
        ];

        foreach ($engines as $categoryId => $name) {
            DB::table('vars')->updateOrInsert(
                ['name' => $categoryId],
                [
                    'value' => $name,
                    'group' => 'Socigames Engine',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
