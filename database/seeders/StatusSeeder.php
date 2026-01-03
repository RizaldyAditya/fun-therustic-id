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

        DB::table('statuses')->insert([
            'name'       => 'To Download',
            'slug'       => 'to-download',
            'text_color' => '#000000',
            'bg_color'   => '#fff705',
            'order'      => 7,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Plan to Play',
            'slug'       => 'plan-to-play',
            'text_color' => '#3d3d3d',
            'bg_color'   => '#e6e6e6',
            'order'      => 8,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Playing',
            'slug'       => 'playing',
            'text_color' => '#0a53a8',
            'bg_color'   => '#bfe1f6',
            'order'      => 9,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Waiting for Update',
            'slug'       => 'waiting-for-update',
            'text_color' => '#11734b',
            'bg_color'   => '#d4edbc',
            'order'      => 10,
            'is_active'  => true,
        ]);

        DB::table('statuses')->insert([
            'name'       => 'Abandoned',
            'slug'       => 'abandoned',
            'text_color' => '#ffcfc9',
            'bg_color'   => '#b10202',
            'order'      => 11,
            'is_active'  => true,
        ]);
    }
}
