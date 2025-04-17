<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\NotificationResource;
use App\Services\CommonService;

class NotificationController extends Controller
{
    /**
     * Get List Languages
     */
    public function list($profileId)
    {
        try {
            $data = CommonService::getModel('Notification')->getList($profileId);

            return $this->sendResponseApi(['data' => NotificationResource::collection($data), 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

}
