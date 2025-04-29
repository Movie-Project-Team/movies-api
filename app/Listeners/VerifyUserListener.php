<?php

namespace App\Listeners;

use App\Events\VerifyUserEvent;
use App\Notifications\MailTemplate;
use App\Services\CommonService;
use App\Support\Constants;
use App\Support\Helper;
use Illuminate\Support\Facades\Log;

class VerifyUserListener
{
    /**
     * Handle the event.
     */
    public function handle(VerifyUserEvent $event): void
    {
        $password = Helper::generateNumber(4);
        $user = CommonService::getModel('User')->getDetail($event->userId);

        $user->update([
            'is_active' => Constants::STATUS_ACTIVE
        ]);

        CommonService::getModel('Profile')->update([
            'password' => $password
        ], $user->profile->id);

        $user->notify(new MailTemplate('verify_user_success',[
            'password' => $password,
        ]));
    }
}
