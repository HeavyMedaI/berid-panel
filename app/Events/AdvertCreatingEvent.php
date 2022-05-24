<?php
/*
 * File name: AdvertCreatingEvent.php
 * Last modified: 2021.09.15 at 13:30:06
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Events;

use App\Models\Advert;
use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdvertCreatingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Advert $advert)
    {
        if (!empty($advert->date_pick_up)) {
            $advert->date_pick_up = convertDateTime($advert->date_pick_up);
        }
        if (!empty($advert->date_drop_off)) {
            $advert->date_drop_off = convertDateTime($advert->date_drop_off);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
