<?php

use App\Http\Resources\Client\ProfileResource;
use App\Services\CommonService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('room.{id}', function ($user, $id) {
    $profileId = Request::header('X-Profile-Id');
    $profile = CommonService::getModel('Profile')->getDetail($profileId);
    return new ProfileResource($profile);
});


