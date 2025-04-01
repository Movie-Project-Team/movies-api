<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\WatchHistoryResource;
use App\Services\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WatchHistoryController extends Controller
{
    /**
     * Get List Profile by Account
     */
    public function list(Request $request, $profileId)
    {
        try {
            $data = CommonService::getModel('WatchHistory')->getList($profileId);

            return $this->sendResponseApi(['data' => WatchHistoryResource::collection($data), 'message' => 'Save process success', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Get Profile by Account
     */
    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = CommonService::getModel('WatchHistory')->upsert([
                'profile_id' => $request->profileId,
                'movie_id' => $request->movieId
            ], [
                'profile_id' => $request->profileId,
                'movie_id' => $request->movieId,
                'time_process' => $request->timeProcess,
                'episode' => $request->episode,
                'last_watched_at' => $request->lastWatchedAt,
            ]);

            DB::commit();
            return $this->sendResponseApi(['data' => new WatchHistoryResource($data), 'message' => 'Save process success', 'code' => 200]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
