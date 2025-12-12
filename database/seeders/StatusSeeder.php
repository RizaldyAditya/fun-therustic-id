<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            'name'       => 'On-Going',
            'slug'       => 'on-going',
            'text_color' => '#11734b',    
            'bg_color'   => '#d4edbc',
            'order'      => 1,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Plan to Watch',
            'slug'       => 'plan-to-watch',
            'text_color' => '#5a3286',
            'bg_color'   => '#e6cff2',
            'order'      => 2,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Watching',
            'slug'       => 'watching',
            'text_color' => '#473821',
            'bg_color'   => '#ffe5a0',
            'order'      => 3,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'On-Hold',
            'slug'       => 'on-hold',
            'text_color' => '#ffc8aa',
            'bg_color'   => '#ffffff',
            'order'      => 4,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Completed',
            'slug'       => 'completed',
            'text_color' => '#000000',
            'bg_color'   => '#ffffff',
            'order'      => 5,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Dropped',
            'slug'       => 'Dropped',
            'text_color' => '#ffcfc9',
            'bg_color'   => '#b10202',
            'order'      => 6,
            'is_active'  => true,
        ]);
    }
}
