<?php
namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class Helper  {
   public static function generateNumber(int $length = 6) {
        $otp = implode('', Arr::random(range(0, 9), $length));
        return $otp;
   }

   public static function formatDate($date, $format = 'Y-m-d H:i:s')
   {
       if (!$date) {
           return null;
       }

       try {
           if (! $date instanceof Carbon) {
               $date = Carbon::parse($date);
           }
           return $date->format($format);
       } catch (\Exception $e) {
           return null;
       }
   }
}