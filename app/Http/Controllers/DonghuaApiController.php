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

    /**
     * Return a paginated list of latest episodes.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latestEpisodes(Request $request)
    {
        // Validate pageSize - must be one of: 10, 20, 50, 100
        $pageSize = (int) $request->get('page_size', 10);
        $allowedPageSizes = [10, 20, 50, 100];
        if (!in_array($pageSize, $allowedPageSizes)) {
            $pageSize = 10;
        }

        // Get search query
        $search = $request->get('q', '');
        $stream_id = $request->get('stream_id', null);

        // Get sort parameters
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Validate sort_by column
        $allowedSortColumns = ['created_at', 'episode_number', 'donghua_id'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Build query with eager loading
        $query = Episode::with(['donghua', 'stream'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('donghua', function ($subQuery) use ($search) {
                    $subQuery->where('title_en', 'LIKE', "%{$search}%")
                        ->orWhere('title_zh', 'LIKE', "%{$search}%");
                });
            })
            ->when(!empty($stream_id) && is_numeric($stream_id), function ($q) use ($stream_id) {
                $q->where('stream_id', $stream_id);
            })
            ->orderBy($sortBy, $sortOrder);

        // Paginate results
        $episodes = $query->paginate($pageSize);

        // Format response
        $data = $episodes->getCollection()->map(function ($episode) {
            return [
                'id' => $episode->id,
                'title' => $episode->donghua->title_en ?? '',
                'donghua_id' => $episode->donghua->id ?? null,
                'episode_number' => (int) $episode->episode_number,
                'episode_dl' => $episode->donghua->episode_dl ?? '',
                'stream_name' => $episode->stream->name ?? null,
                'created_at' => $episode->created_at ? $episode->created_at->format('Y-m-d H:i:s') : null,
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
     * @param Request $request
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
        if (empty($episodeIds) || !is_array($episodeIds)) {
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
            if (!$donghua) continue;
            
            $donghuaId = $donghua->id;
            
            // Initialize donghua entry if not exists
            if (!isset($donghuaMap[$donghuaId])) {
                $donghuaMap[$donghuaId] = [
                    'title' => $donghua->title_en,
                    'path' => $donghua->local_download_path,
                    'episodes' => []
                ];
            }
            
            // Map episode_number to video_source_url
            $donghuaMap[$donghuaId]['episodes'][$episode->episode_number] = $episode->video_source_url;
        }
        
        return response()->json(array_values($donghuaMap));
    }

    /**
     * Update the episode_dl field of a donghua.
     *
     * @param Request $request
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
