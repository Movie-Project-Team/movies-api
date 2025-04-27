<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\MovieLiteResource;
use App\Http\Resources\Client\MovieResource;
use App\Services\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    public function list(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $item = $request->input('item', 10);
            $keyword = $request->input('keyword', '');
            $language = $request->input('lang', '');
            $genre = $request->input('gen', '');
            $type = $request->input('type', '');
            $status = $request->input('status', '');
            $year = $request->input('year', '');
    
            $data = CommonService::getModel('Movies')->getList([
                'page' => $page,
                'item' => $item,
                'keyword' => $keyword,
                'lang' => $language,
                'gen' => $genre,
                'type' => $type,
                'status' => $status,
                'year' => $year,
            ]);
    
            return $this->getListPaginate(MovieLiteResource::collection($data));
        } catch (\Exception $e) {
            Log::error('Error in getList method', ['message' => $e->getMessage()]);
    
            return $this->sendErrorApi($e->getMessage());
        }
    }

    public function detail(Request $request, $slug)
    {
        try {
            $data = CommonService::getModel('Movies')->getDetailBySlug($slug);
            if (!$data) {
                return $this->sendErrorApi('Data not found');
            }

            return $this->getDetailData(new MovieResource($data));
        } catch (\Exception $e) {
            Log::error('Error in get method', ['message' => $e->getMessage()]);
    
            return $this->sendErrorApi($e->getMessage());
        }
    }

    public function ranking($type)
    {
        try {
            $data = CommonService::getModel('Movies')->getRankingByType($type);
            if (!$data) {
                return $this->sendErrorApi('Data not found');
            }

            return $this->sendResponseApi(['data' => MovieResource::collection($data), 'code' => 200]);
        } catch (\Exception $e) {
            Log::error('Error in get method', ['message' => $e->getMessage()]);
    
            return $this->sendErrorApi($e->getMessage());
        }
    }

    public function recommend($id)
    {
        try {
            $data = CommonService::getModel('Movies')->recommendList($id);
            if (!$data) {
                return $this->sendErrorApi('Data not found');
            }

            return $this->sendResponseApi(['data' => MovieResource::collection($data), 'code' => 200]);
        } catch (\Exception $e) {
            Log::error('Error in get method', ['message' => $e->getMessage()]);
    
            return $this->sendErrorApi($e->getMessage());
        }
    }
    
}
