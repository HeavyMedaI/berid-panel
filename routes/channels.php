<?php
/**
 * File name: channels.php
 * Last modified: 2019.08.27 at 15:37:13
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
