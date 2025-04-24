<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ChangePasswordProfileRequest;
use App\Http\Requests\Client\GetListProfileRequest;
use App\Http\Requests\Client\GetProfileRequest;
use App\Http\Requests\Client\VerifyPasswordProfileRequest;
use App\Http\Resources\Client\FavouriteResource;
use App\Http\Resources\Client\ProfileResource;
use App\Services\CommonService;
use Illuminate\Http\Request;

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

            return $this->getDetailData(new ProfileResource($profile));
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

            return $this->sendResponseApi(['data' => new ProfileResource($profile), 'message' => 'Password is correct', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function addFavorite(Request $request)
    {
        try {
            $data = CommonService::getModel('Favorite')->create([
                'movie_id' => $request->movieId,
                'profile_id' => $request->profileId
            ]);

            return $this->sendResponseApi(['data' => $data, 'message' => 'Add Success', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function removeFavorite(Request $request)
    {
        try {
            $data = CommonService::getModel('Favorite')-> getByMovieAndProfile($request->movieId, $request->profileId);

            if ($data) {
                $data->delete();
                return $this->sendResponseApi(['message' => 'Remove Success', 'code' => 200]);
            }

            return $this->sendResponseApi(['message' => 'Favorite not found', 'code' => 404]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
