<?php

namespace App\Models;

class Notification extends BaseRepository
{
    public function __construct() {
        parent::__construct('Notifications');
    }

    public function getList($profileId) {
        $whereHas = [
            [
                'profiles',
                function ($query) use ($profileId) {
                    $query->where('user_notification.profile_id', $profileId);
                }
            ]
        ];
    
        return $this->getData([
            'type' => 3,
            'whereHas' => $whereHas
        ]);
    }
    
}
