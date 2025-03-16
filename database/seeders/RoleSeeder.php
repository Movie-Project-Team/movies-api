<?php

namespace Database\Seeders;

use App\Services\CommonService;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommonService::getModel('Roles')->createMulti([
            [
                'name' => 'super admin',
            ],
            [
                'name' => 'admin',
            ]
        ]);
    }
}
