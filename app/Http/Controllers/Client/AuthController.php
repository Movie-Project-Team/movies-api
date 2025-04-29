<?php

namespace App\Http\Controllers\Client;

use App\Events\SendOtpEvent;
use App\Events\VerifyUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AuthRequest;
use App\Http\Requests\Client\ChangePassworUserRequest;
use App\Http\Requests\Client\VerifyOTPUserRequest;
use App\Services\CommonService;
use App\Support\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class AuthController extends Controller
{
    public function register(AuthRequest $request){
        DB::beginTransaction();
        
        try {
            $data = $request->all();

            $data['password'] = Hash::make($data['password']);
            $data['permission'] = Constants::PERMISSION_ADULT;

            $user = CommonService::getModel('User')->create($data);
            $token = $user->createToken('auth_token')->plainTextToken;

            // create temp profile
            CommonService::getModel('Profile')->create([
                'user_id' => $user->id,
                'name' => $request->name,
                'avatar' => Arr::random(config('image.profile_thumb'))
            ]);

            // send otp
            event(new SendOtpEvent($user));

            DB::commit();
            return $this->sendResponseApi(['data' => $user, 'code' => 200, 'token' => $token]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
        
    }

    public function login(AuthRequest $request){

        try {
            $data = $request->all();

            $user = CommonService::getModel('User')->getDetailByEmail($data['email']);

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return $this->sendResponseApi(['message' => 'The provided credentials are incorrect.', 'code' => 401]);
            }

            $accessToken = $user->createToken('auth_token');
            $accessToken->accessToken->expires_at = Carbon::now()->addHours(12);
            $accessToken->accessToken->save();
            
            $token = $accessToken->plainTextToken;
            
            return $this->sendResponseApi(['data' => $user, 'code' => 200, 'token' => $token]);
        } catch (\Exception $e) {

            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
        
    }

    /**
     * Change Password User
     */
    public function changePasswordUser(ChangePassworUserRequest $request)
    {
        try {
            $user = CommonService::getModel('User')->getDetail($request['userId']);

            if (!$user) {
                return $this->sendResponseApi(['message' => 'User not found', 'code' => 404]);
            }

            if (!Hash::check($request['old_password'], $user->password)) {
                return $this->sendResponseApi(['message' => 'Old password is incorrect', 'code' => 400]);
            }

            // update password
            $user->update([
               'password' => Hash::make($request['new_password']),
            ]);

            return $this->sendResponseApi(['message' => 'Password updated successfully', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Verify Password User
     */
    public function verifyOTPUser(VerifyOTPUserRequest $request)
    {
        try {
            $otp = CommonService::getModel('UserOtp')->getDetailByUserAndOtp($request->userId, $request->otp);

            if (!$otp) {
                return $this->sendResponseApi(['message' => 'Invalid OTP. Please check your OTP and try again.', 'code' => 404]);
            }

            if ($otp->expired_at < Carbon::now()->format('Y-m-d H:i:s')) {
                // Return errors 
                return $this->sendResponseApi([
                    'code' => 400,
                    'error' => 'OTP has expired'
                ]);
            }

            event(new VerifyUserEvent($request->userId));

            return $this->sendResponseApi(['message' => 'Success', 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
