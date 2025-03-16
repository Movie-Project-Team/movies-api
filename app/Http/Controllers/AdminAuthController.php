<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAdminRequest;
use App\Services\CommonService;
use Hash;

class AdminAuthController extends Controller
{
    /**
     * Get List Genres
     */
    public function loginAdmin(LoginAdminRequest $request)
    {
        $admin = CommonService::getModel(   'Admin')->findByAttributes(['email' => $request->email]);
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->sendResponseApi(['message' => 'Email or Password is incorrect', 'code' => 401]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return $this->sendResponseApi(['message' => 'Login Success', 'data' => [$admin,$token]]);

    }
}
