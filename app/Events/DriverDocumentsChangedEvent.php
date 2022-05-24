<?php
/*
 * File name: DriverDocumentsChangedEvent.php
 * Last modified: 2021.06.09 at 15:53:58
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Events;

use App\Models\DriverDocuments;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverDocumentsChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver_documents;
    public $moderator;
    public $user;

    /**
     * AdvertChangedEvent constructor.
     * @param DriverDocuments $driver_documents
     * @param User $user
     * @param User $moderator
     */
    public function __construct($driver_documents, $moderator, $user)
    {
        $this->driver_documents = $driver_documents;
        $this->moderator = $moderator;
        $this->user_id = $user;
    }


}
