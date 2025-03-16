<?php

namespace Database\Seeders;

use App\Services\CommonService;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommonService::getModel('Admin')->createMulti([
            [
                'name' => 'super_admin',
                'email' => 'truongdeptrai@gmail.com',
                'password' => Hash::make('1102'),
            ],
            [
                'name' => 'admin',
                'email' => 'hieu100tr@gmail.com',
                'password' => Hash::make('1234'),
            ]
        ]);
    }
}
