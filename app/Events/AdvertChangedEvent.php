<?php
/*
 * File name: AdvertChangedEvent.php
 * Last modified: 2021.06.09 at 15:53:58
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Events;

use App\Models\Advert;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdvertChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $advert;
    public $user;
    public $driver;

    /**
     * AdvertChangedEvent constructor.
     * @param Advert $advert
     * @param User $user
     * @param User $driver
     */
    public function __construct($advert, $user, $driver)
    {
        $this->advert = $advert;
        $this->user = $user;
        $this->driver = $driver;
    }


}
