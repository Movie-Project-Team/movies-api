<?php

namespace App\Listeners;

use App\Events\SendOtpEvent;
use App\Notifications\MailTemplate;
use App\Services\CommonService;
use App\Support\Helper;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtpListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendOtpEvent $event): void
    {
        $otp = Helper::generateNumber(4);

        $user = CommonService::getModel('User')->getDetailByEmail($event->user->email);

        CommonService::getModel('UserOtp')->create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $user->notify(new MailTemplate('send_otp',[
            'code' => $otp,
        ]));
    }
}
