<?php

namespace App\Models;

class UserOtp extends BaseRepository
{
    public function __construct() {
        parent::__construct('UserOtp');
    }

    public function getDetailByUserAndOtp($userId, $otp) {
        return $this->getData([
            'type' => 1,
            'where' => [
                'user_id' => $userId,
                'otp' => $otp
            ]
        ]);
    }

    public function getDetailByUser($userId) {
        return $this->getData([
            'type' => 1,
            'where' => [
                'user_id' => $userId
            ]
        ]);
    }
}
