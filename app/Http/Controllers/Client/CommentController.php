<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CommentResource;
use App\Services\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function list($movieId)
    {
        try {
            $data = CommonService::getModel('Comments')->getList($movieId);
            
            return $this->sendResponseApi(['data' => CommentResource::collection($data), 'code' => 200]);
        } catch (\Exception $e) {
            Log::error('Error in getList method', ['message' => $e->getMessage()]);
    
            return $this->sendErrorApi($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $data = CommonService::getModel('Comments')->create([
                'user_id' => $request->userId,
                'movie_id' => $request->movieId,
                'parent_id' => $request->parentId ?? null,
                'content' => $request->content,
                'is_approved' => 1
            ]);

            DB::commit();
            return $this->getDetailData(new CommentResource($data));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in getList method', ['message' => $e->getMessage()]);
            return $this->sendErrorApi($e->getMessage());
        }
    }
}
