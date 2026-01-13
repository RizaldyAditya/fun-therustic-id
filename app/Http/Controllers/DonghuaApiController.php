<?php

namespace App\Http\Controllers;

use App\Models\Donghua;
use App\Models\Episode;
use App\Models\Stream;
use App\Traits\Utilities;
use Illuminate\Http\Request;

class DonghuaApiController extends Controller
{
    use Utilities;

    /**
     * Search for donghua by title.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        if (empty($query)) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $results = Donghua::where('title_en', 'LIKE', "%{$query}%")
            ->orWhere('title_zh', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $this->apiDonghuaDetail($results),
        ]);
    }

    /**
     * Return a list of recommended donghua with their episodes and streaming sources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forYou()
    {
        $forYou = Donghua::where('image_cover', '!=', '')
            ->where('trending_sort', '<', 99)
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $this->apiDonghuaDetail($forYou),
        ]);
    }

    /**
     * Return a list of the latest episodes from the past 72 hours.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        $episodes = Episode::with('donghua')
            ->where('created_at', '>=', now()->subHours(72))
            ->limit(20)
            ->latest()
            ->get()
            ->unique('donghua_id');

        return response()->json([
            'status' => 'success',
            'data' => $this->apiDonghuaEpisode($episodes),
        ]);
    }

    /**
     * Return a list of trending donghua with their episodes and streaming sources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trending()
    {
        $trendings = Donghua::where('is_hot', 1)
            ->where('is_airing', 1)
            ->where('image_cover', '!=', '')
            ->limit(20)
            ->inRandomOrder()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $this->apiDonghuaDetail($trendings),
        ]);
    }

    /**
     * Return a single donghua by id with its episodes and streaming sources.
     *
     * @param int $id The id of the donghua.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $donghua          = Donghua::where('id', $id)->get();
        $streamingSources = Stream::whereHas('episodes', function ($query) use ($id) {
            $query->where('donghua_id', $id);
        })->with(['episodes' => function ($query) use ($id) {
            $query->where('donghua_id', $id)->latest('episode_number');
        }])->get();

        return response()->json([
            'status' => 'success',
            'donghua' => $this->apiDonghuaDetail($donghua)->first(),
            'streaming_sources' => $streamingSources->map(fn($stream) => [
                'source' => $stream->name,
                'episodes' => $stream->episodes->map(fn($ep) => [
                    'number' => $ep->episode_number,
                    'url' => $ep->video_source_url,
                    'created_at' => $ep->created_at->format('F d, Y'),
                ]),
            ])->values(),
        ]);
    }
}
