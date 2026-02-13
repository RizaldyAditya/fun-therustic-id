<?php

namespace App\Http\Controllers;

use App\Models\Donghua;
use App\Models\Episode;
use App\Models\Stream;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonghuaApiController extends Controller
{
    use Utilities;

    /**
     * Search for donghua by title.
     *
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
     * @param  int  $id  The id of the donghua.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $id = $request->route('id');
        if (empty($id)) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $episode = Episode::where('id', $id)->with(['donghua', 'stream', 'donghua.studio'])->get();
        // $streamingSources = Stream::whereHas('episodes', function ($query) use ($id) {
        //     $query->where('donghua_id', $id);
        // })->with(['episodes' => function ($query) use ($id) {
        //     $query->where('donghua_id', $id)->latest('episode_number');
        // }])->get();

        return response()->json([
            'status' => 'success',
            'data' => $this->apiDonghuaEpisode(collect($episode))->first(),
        ]);
    }

    /**
     * Return a paginated list of latest episodes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latestEpisodes(Request $request)
    {
        // Validate pageSize
        $pageSize = (int) $request->get('page_size', 50);

        // Get search query
        $search = $request->get('q', '');
        $stream_id = $request->get('stream_id', null);

        // Get sort parameters
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Validate sort_by column
        $allowedSortColumns = ['created_at', 'episode_number', 'donghua_id'];
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Build query with eager loading - use JOIN instead of whereHas for better performance
        $query = Episode::with(['donghua', 'stream'])
            ->when($search, function ($q) use ($search) {
                // Join with donghuas table for searching on donghua titles
                $q->join('donghuas', 'donghua_episodes.donghua_id', '=', 'donghuas.id')
                    ->where(function ($q) use ($search) {
                        $q->where('donghuas.title_en', 'LIKE', "%{$search}%")
                            ->orWhere('donghuas.title_zh', 'LIKE', "%{$search}%")
                            ->orWhere('donghua_episodes.title', 'LIKE', "%{$search}%");
                    })
                    ->select('donghua_episodes.*'); // Ensure we select from episodes table
            }, function ($q) {
                // When no search, still select from episodes
                $q->select('donghua_episodes.*');
            })
            ->when(! empty($stream_id) && is_numeric($stream_id), function ($q) use ($stream_id) {
                $q->where('stream_id', $stream_id);
            })
            ->orderBy($sortBy, $sortOrder);

        // Paginate results
        $episodes = $query->paginate($pageSize);

        // Format response
        $data = $episodes->getCollection()->map(function ($episode) {
            return [
                'id' => $episode->id,
                'title' => $episode->title ?? '',
                'donghua_id' => (int) $episode->donghua->id ?? 0,
                'donghua_title' => $episode->donghua->title_en ?? '',
                'donghua_cover_image' => asset('storage/'.$episode->donghua->image_cover ?? ''),
                'season' => (int) $episode->donghua->season ?? 0,
                'episode_number' => (int) $episode->episode_number,
                'episode_watched' => (int) $episode->donghua->episode_watched ?? 0,
                'episode_watched_seasonal' => (int) $episode->donghua->episode_watched_seasonal ?? 0,
                'episode_latest' => (int) $episode->donghua->episode_latest ?? 0,
                'episode_dl' => (int) $episode->donghua->episode_dl ?? 0,
                'video_source_url' => $episode->video_source_url['english']['dailymotion'] ?? $episode->video_source_url['english']['ok_ru'] ?? null,
                'stream_id' => $episode->stream->id ?? null,
                'stream_name' => $episode->stream->name ?? null,
                'stream_url' => $episode->stream_url ?? null,
                'mc_name' => $episode->donghua->mc_name ?? null,
                'notes' => $episode->notes ?? null,
                'airing_status' => $episode->donghua->is_airing ?? 0,
                'is_hot' => $episode->donghua->is_hot ?? 0,
                'synopsis' => $episode->donghua->synopsis ?? '',
                'studio' => $episode->donghua->studio->name ?? '',
                'local_download_path' => $episode->donghua->local_download_path ?? '',
                'created_at' => $episode->created_at ? $episode->created_at->format('Y-m-d H:i:s') : null,
                'donghua' => $this->apiDonghuaDetail(collect([$episode->donghua]))->first(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data->values(),
            'pagination' => [
                'current_page' => $episodes->currentPage(),
                'per_page' => $episodes->perPage(),
                'total' => $episodes->total(),
                'last_page' => $episodes->lastPage(),
            ],
        ]);
    }

    /**
     * Return JSON data for the downloader app.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloaderJson(Request $request)
    {
        // Get episode IDs from request - support both JSON body array and query parameter
        $episodeIds = $request->input('episode_ids');

        // If episode_ids is a string, try to decode it as JSON
        if (is_string($episodeIds)) {
            $decoded = json_decode($episodeIds, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $episodeIds = $decoded;
            }
        }

        // If no episode_ids in request body, check query parameters
        if (empty($episodeIds)) {
            $episodeIds = $request->query('episode_ids');

            // If query param is a string representation of array, decode it
            if (is_string($episodeIds)) {
                $decoded = json_decode($episodeIds, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $episodeIds = $decoded;
                }
            }
        }

        // Ensure we have an array of IDs
        if (empty($episodeIds) || ! is_array($episodeIds)) {
            return response()->json([]);
        }

        // Fetch episodes with their associated donghua
        $episodes = Episode::whereIn('id', $episodeIds)
            ->with('donghua')
            ->get();

        // Group episodes by donghua
        $donghuaMap = [];

        foreach ($episodes as $episode) {
            $donghua = $episode->donghua;
            if (! $donghua) {
                continue;
            }

            $donghuaId = $donghua->id;

            // Initialize donghua entry if not exists
            if (! isset($donghuaMap[$donghuaId])) {
                $donghuaMap[$donghuaId] = [
                    'title' => $donghua->title_en,
                    'path' => $donghua->local_download_path,
                    'episodes' => [],
                ];
            }

            // Map episode_number to video_source_url
            $url = $episode->video_source_url['english']['dailymotion'] ?? $episode->video_source_url['english']['ok_ru'] ?? null;
            $donghuaMap[$donghuaId]['episodes'][$episode->episode_number] = $url;
        }

        return response()->json(array_values($donghuaMap));
    }

    /**
     * Update an episode.
     *
     * @return \Illuminate\Http\JsonResponse
     **/
    public function updateEpisode(Request $request, Episode $episode)
    {
        return DB::transaction(function () use ($request, $episode) {
            // 1. Filter Episode Data
            // This removes null, 0, false, and ""
            $episodeData = array_filter($request->only([
                'title',
                'episode_number',
                'stream_id',
                'stream_url',
                'video_source_url',
                'notes',
            ]));

            if (! empty($episodeData)) {
                $episode->update($episodeData);
            }

            // 2. Filter Donghua Data
            if ($request->has('donghua')) {
                $donghuaData = array_filter($request->input('donghua'));

                if (! empty($donghuaData)) {
                    // This updates the related Donghua model directly
                    $episode->donghua()->update($donghuaData);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $episode->fresh('donghua'),
            ]);
        });
    }

    /**
     * Update the episode_dl field of a donghua.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEpisodeDL(Request $request)
    {
        $donghuaId = $request->post('donghua_id');
        $episodeDl = $request->post('episode_dl');

        if (empty($donghuaId) || empty($episodeDl)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing donghua_id or episode_dl',
            ]);
        }

        Donghua::where('id', $donghuaId)->update(['episode_dl' => $episodeDl]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
