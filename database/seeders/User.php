<?php

namespace Database\Seeders;

use App\Services\CommonService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('vi_VN');

        foreach (range(1, 100) as $i) {
            CommonService::getModel('User')->createMulti([
                [
                    'email' => $faker->email,
                    'password' => $faker->numberBetween(1000,9999),
                    'permission' => $faker->randomElement([1, 2]),
                ]
            ]);
        }
    }
}
