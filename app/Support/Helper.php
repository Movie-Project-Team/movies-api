<?php
namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class Helper  {
   public static function generateNumber(int $length = 6) {
        $otp = implode('', Arr::random(range(0, 9), $length));
        return $otp;
   }
}