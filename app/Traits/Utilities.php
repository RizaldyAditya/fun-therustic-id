<?php

namespace App\Traits;

trait Utilities
{
    /**
     * Sanitizes and extracts the correct episode number from raw crawler text.
     * Handles ranges (144-145), "to" patterns, and preview commas.
     */
    public function sanitizeEpisodeNumber(string $rawText): array
    {
        $episodeNumber = $rawText;

        // 1. Extract bracketed info [xxx] to preserve it (e.g., [Sub])
        preg_match_all('/\[([^\]]*)\]/', $episodeNumber, $matches);
        $brackets = !empty($matches[0]) ? $matches[0][0] : '';

        // 2. Handle "Ep 476 to 477" -> Take 477
        if (preg_match('/(\d+)\s+to\s+(\d+)/i', $episodeNumber, $toMatches)) {
            $episodeNumber = $toMatches[2];
        }

        // 3. Handle "Ep 143, 144 Preview" -> Take 143 (Remove after comma)
        if (str_contains($episodeNumber, ',')) {
            $episodeNumber = explode(',', $episodeNumber)[0];
        }

        // 4. Handle Merged Episodes (e.g., 144-145) -> Take 145
        if (preg_match('/(\d+)\s*[\-\~]\s*(\d+)/', $episodeNumber, $rangeMatches)) {
            $episodeNumber = $rangeMatches[2];
        }

        // 5. Cleanup strings and non-numeric junk
        $episodeNumber = preg_replace('/\[.*?\]/', '', $episodeNumber);
        $episodeNumber = preg_replace('/\((.*?)\)/', '', $episodeNumber);
        $cleanNumber   = preg_replace('/[^0-9]/', '', $episodeNumber);

        // 6. Final integer for logic and display string
        $intVal     = (int) $cleanNumber;
        $displayVal = $cleanNumber . ($brackets ? ' ' . $brackets : '');

        return [
            'int' => $intVal,
            'display' => $displayVal,
        ];
    }

    public function apiDonghuaDetail($data)
    {
        return $data->map(fn($donghua) => [
            'id' => $donghua->id,
            'title' => $donghua->title_en ?? '',
            'title_cn' => $donghua->title_zh ?? '',
            'synopsis' => $donghua->synopsis ?? '',
            'poster' => asset('storage/' . $donghua->image_cover),
            'season' => (int) $donghua->season ?? '',
            'episode_latest' => (int) $donghua->episode_latest ?? '',
            'episode_total' => (int) $donghua->episode_total ?? '',
            'is_airing' => (bool) $donghua->is_airing,
            'is_hot' => (bool) $donghua->is_hot,
            'studio' => $donghua->studio->name ?? '',
            'source' => $donghua->source->name ?? '',
            'trending_sort' => $donghua->trending_sort ?? 99,
            'created_at' => $donghua->created_at ? $donghua->created_at->diffForHumans() : '',
        ]);
    }

    public function apiDonghuaEpisode($data)
    {
        return $data->map(fn($episode) => [
            'id' => $episode->id,
            'title' => $episode->donghua->title_en ?? '',
            'episode_number' => (int) $episode->episode_number,
            'created_at' => $episode->created_at ? $episode->created_at->diffForHumans() : $episode->created_at->format('F d, Y'),
            'donghua' => $this->apiDonghuaDetail(collect([$episode->donghua]))->first(),
        ]);
    }
}
