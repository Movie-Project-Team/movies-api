<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CommentResource;
use App\Services\CommonService;
use App\Support\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
                'profile_id' => $request->profileId,
                'movie_id' => $request->movieId,
                'parent_id' => $request->parentId ?? null,
                'content' => $request->content,
                'is_approved' => 1
            ]);

            if ($request->parentId) {
                $parent = CommonService::getModel('Comments')->getDetailById($request->parentId);
                $movie = CommonService::getModel('Movies')->getDetail($request->movieId);

                $notification = CommonService::getModel('Notifications')->create([
                    'title' =>  "Bình luận mới",
                    'message' => "Người dùng {$data->profile->name} đã trả lời bình luận của bạn về bộ phim {$movie->title}!",
                    'type' => Constants::NOTIFICATON_TYPE_INFO,
                    'link' => config('site.detail') . $movie->slug
                ]);

                CommonService::getModel('UserNotification')->create([
                    'profile_id' =>  $parent->profile_id,
                    'notification_id' => $notification->id,
                ]);
            }

            DB::commit();
            return $this->getDetailData(new CommentResource($data));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in getList method', ['message' => $e->getMessage()]);
            return $this->sendErrorApi($e->getMessage());
        }
    }
}
