<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CommonService;

class LanguagesController extends Controller
{
    /**
     * Get List Languages
     */
    public function getListLanguages()
    {
        try {
            $languages = CommonService::getModel('Languages')->getList();
            return $this->sendResponseApi(['data' => $languages, 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

}
