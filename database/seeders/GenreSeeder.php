<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            // --- Common/Standard ---
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
            'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Supernatural', 'Thriller',

            // --- Visual Novel Specific ---
            'Visual Novel', 'Kinetic Novel', 'Dating Sim', 'Nukige', 'Eroge',
            'Otome', 'Bara', 'Yuri', 'Yaoi', 'Nakige', 'Utsuge',

            // --- Themes & Tropes ---
            'Harem', 'Reverse Harem', 'Incest', 'NTR', 'Netorare', 'School Life',
            'Isekai', 'Cyberpunk', 'Steampunk', 'Post-Apocalyptic', 'Military',
            'Magic', 'Mecha', 'Psychological', 'Dystopian', 'Tragedy',

            // --- Demographics ---
            'Shounen', 'Shoujo', 'Seinen', 'Josei',

            // --- Dark/Adult Themes (Content Warnings) ---
            'Gore', 'Violence', 'Dark Fantasy', 'Survival', 'Corruption',
            'Mind Control', 'Rape', 'Torture', 'Abuse', 'BDSM',
        ];

        foreach ($genres as $genre) {
            Genre::updateOrCreate(
                ['name' => $genre]
            );
        }
    }
}
