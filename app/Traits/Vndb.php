<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Vndb
{
    /*
     * Get VN data from VNDB
     * @param string $id
     * @return array|null
     */
    public static function getVnData($id): ?array
    {
        $response = Http::post(config('app.vndb_endpoint').'/vn', [
            'filters' => [
                'id', '=', $id,
            ],
            'fields' => 'title, devstatus, released, platforms, description, image{id, url, sexual, violence, thumbnail}, developers{name, original, type, description, lang}',
            'sort' => 'title',
            'reverse' => false,
            'results' => 5,
            'page' => 1,
            'count' => true,
            'compact_filters' => false,
            'normalized_filters' => false,
        ]);
        if ($response->status() === 200) {
            return $response->json()['results'][0] ?? null;
        }

        return null;
    }

    /*
     * Get character data from VNDB
     * @param string $id
     * @return array
     */
    public static function getCharactersData($id): array
    {
        $response = Http::post(config('app.vndb_endpoint').'/character', [
            'filters' => ['vn', '=', ['id', '=', $id]],
            'fields' => 'id, name, original, description, age, sex, gender, birthday, image{id, url}, vns{role}',
            'sort' => 'id',
            'reverse' => false,
            'results' => 100,
            'page' => 1,
            'count' => false,
            'compact_filters' => false,
            'normalized_filters' => false,
        ]);

        if ($response->status() === 200) {
            return $response->json()['results'] ?? [];
        }

        return [];
    }
}
