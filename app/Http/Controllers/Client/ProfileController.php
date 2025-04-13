<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ChangePasswordProfileRequest;
use App\Http\Requests\Client\GetListProfileRequest;
use App\Http\Requests\Client\GetProfileRequest;
use App\Http\Requests\Client\VerifyPasswordProfileRequest;
use Illuminate\Support\Facades\Cache;
use App\Services\CommonService;

class ProfileController extends Controller
{
    /**
     * Get List Profile by Account
     */
    public function getListProfile(GetListProfileRequest $request, $id)
    {
        try {
            $profiles = CommonService::getModel('Profile')->getList($id);
            return $this->sendResponseApi(['data' => $profiles, 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Get Profile by Account
     */
    public function getProfile(GetProfileRequest $request, $id)
    {
        try {
            $profile = CommonService::getModel('Profile')->getDetail($id);

            if (!$profile) {
                return $this->sendResponseApi(['message' => 'Profile not found', 'code' => 404]);
            }

            return $this->sendResponseApi(['data' => $profile, 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Change Password Profile by Account
     */
    public function changePasswordProfile(ChangePasswordProfileRequest $request)
    {
        try {
            $profile = CommonService::getModel('Profile')->getDetail($request['profileId']);

            if (!$profile) {
                return $this->sendResponseApi(['message' => 'Profile not found', 'code' => 404]);
            }

            if ($request['old_password'] !== $profile->password) {
                return $this->sendResponseApi(['message' => 'Old password is incorrect', 'code' => 400]);
            }

            // update password
            $profile->update([
               'password' =>  $request['new_password'],
            ]);

            return $this->sendResponseApi(['message' => 'Password updated successfully', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Verify Password Profile by Account
     */
    public function verifyPasswordProfile(VerifyPasswordProfileRequest $request)
    {
        try {
            $profile = CommonService::getModel('Profile')->getDetail($request['profileId']);

            if (!$profile) {
                return $this->sendResponseApi(['message' => 'Profile not found', 'code' => 404]);
            }
            Cache::put('verified_profile_for_user_' . auth()->id(), $profile->id, now()->addMinutes(30));

            return $this->sendResponseApi(['data' => $profile, 'message' => 'Password is correct', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
