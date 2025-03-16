<?php

namespace App\Models;

class User extends BaseRepository
{
    public function __construct() {
        parent::__construct('User');
    }
    
    public function getDetailByEmail($email)
    {
        return $this->getData([
            'type'  => '1',
            'where' => [
                'email' => $email
            ]]);
    }
}
