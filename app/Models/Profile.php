<?php

namespace App\Models;

class Profile extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('Profile');
    }

    public function getList($id)
    {
        return $this->getData([
            'type' => 2,
            'where' => [
                'user_id' => $id
            ]
        ]);
    }
    
    public function getDetailById($id)
    {
        return $this->getData([
            'type'  => '1',
            'where' => [
                'id' => $id
        ]]);
    }
}
