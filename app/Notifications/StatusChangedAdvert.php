<?php
/*
 * File name: StatusChangedAdvert.php
 * Last modified: 2021.09.15 at 13:28:01
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Notifications;

use App\Models\Advert;
use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedAdvert extends Notification
{
    use Queueable;

    /**
     * @var Advert
     */
    public $advert;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
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
        return (new MailMessage)
            ->markdown("notifications::advert", ['advert' => $this->advert])
            ->subject(trans('lang.notification_your_advert', ['advert_id' => $this->advert->id, 'advert_status' => $this->advert->status->status]) . " | " . setting('app_name', ''))
            ->greeting(trans('lang.notification_your_advert', ['advert_id' => $this->advert->id, 'advert_status' => $this->advert->status->status]))
            ->action(trans('lang.advert_details'), route('adverts.show', $this->advert->id));
    }

    public function toFcm($notifiable): FcmMessage
    {
        $message = new FcmMessage();
        $notification = [
            'title' => trans('lang.notification_status_changed_advert'),
            'body' => trans('lang.notification_your_advert', ['advert_id' => $this->advert->id, 'advert_status' => $this->advert->status->status]),
            'icon' => $this->getAdvertMediaUrl(),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => 'App\\Notifications\\StatusChangedAdvert',
            'status' => 'done',
        ];
        $data = $notification;
        $data['advertId'] = $this->advert->id;
        $message->content($notification)->data($data)->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }

    private function getAdvertMediaUrl(): string
    {
        if ($this->advert->hasMedia('image')) {
            return $this->advert->getFirstMediaUrl('advert_media', 'thumb');
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
            'advert_id' => $this->advert['id'],
        ];
    }
}
