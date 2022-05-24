<?php
/*
 * File name: StatusChangedDriverDocuments.php
 * Last modified: 2021.09.15 at 13:28:01
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Notifications;

use App\Models\Booking;
use App\Models\DriverDocuments;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedDriverDocuments extends Notification
{
    use Queueable;

    /**
     * @var DriverDocuments
     */
    private $driverDocuments;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DriverDocuments $driverDocuments)
    {
        $this->driverDocuments = $driverDocuments;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'fcm', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
            ->subject(trans('lang.notification_driver_documents', ['driver_documents_id' => $this->driverDocuments->id, 'driver_documents_status' => $this->driverDocuments->status]) . " | " . setting('app_name', ''))
            ->markdown("notifications::booking", ['booking' => $this->driverDocuments])
            ->greeting(trans('lang.notification_driver_documents', ['driver_documents_id' => $this->driverDocuments->id, 'driver_documents_status' => $this->driverDocuments->status]))
            ->action(trans('lang.driver_documents_details'), route('driver_documents.show', $this->driverDocuments->id));*/
    }

    public function toFcm($notifiable): FcmMessage
    {
        $message = new FcmMessage();
        $notification = [
            'body' => trans('lang.notification_driver_documents', ['driver_documents_id' => $this->driverDocuments->id, 'driver_documents_status' => $this->driverDocuments->status]),
            'title' => trans('lang.notification_status_changed_driver_documents'),
            'icon' => $this->getEServiceMediaUrl(),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => 'App\\Notifications\\StatusChangedDriverDocuments',
            'status' => 'done',
        ];
        $data = $notification;
        $data['driverDocumentsId'] = $this->driverDocuments->id;
        $message->content($notification)->data($data)->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }

    private function getEServiceMediaUrl(): string
    {
        if ($this->driverDocuments->e_service->hasMedia('image')) {
            return $this->driverDocuments->e_service->getFirstMediaUrl('image', 'thumb');
        } else {
            return asset('images/image_default.png');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'driver_documents_id' => $this->driverDocuments['id'],
        ];
    }
}
