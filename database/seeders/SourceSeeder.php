<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sources')->insert(['name' => 'Novel']);
        DB::table('sources')->insert(['name' => 'Manga']);
        DB::table('sources')->insert(['name' => 'Web Novel']);
        DB::table('sources')->insert(['name' => 'Web Manga']);
        DB::table('sources')->insert(['name' => 'Original Story']);
    }
}
