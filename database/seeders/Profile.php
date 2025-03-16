<?php

namespace Database\Seeders;

use App\Services\CommonService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Profile extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('vi_VN');

        foreach (range(1, 50) as $i) {
            CommonService::getModel('Profile')->createMulti([
                [
                    'user_id' => $faker->numberBetween(1, 100),
                    'name' => $faker->userName,
                    'password' => $faker->numberBetween(1000,9999),
                ],
                [
                    'user_id' => $faker->numberBetween(1, 100),
                    'name' => $faker->userName,
                    'password' => $faker->numberBetween(1000,9999),
                ]
            ]);
        }
    }
}
