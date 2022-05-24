<?php
/*
 * File name: NewAdvert.php
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

class NewAdvert extends Notification
{
    use Queueable;

    /**
     * @var Advert
     */
    private $advert;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Advert $advert)
    {
        //
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
            ->subject(trans('lang.notification_new_advert', ['advert_id' => $this->advert->id, 'user_name' => $this->advert->user->name]) . " | " . setting('app_name', ''))
            ->greeting(trans('lang.notification_new_advert', ['advert_id' => $this->advert->id, 'user_name' => $this->advert->user->name]))
            ->action(trans('lang.advert_details'), route('adverts.show', $this->advert->id));
    }

    public function toFcm($notifiable): FcmMessage
    {
        $message = new FcmMessage();
        $notification = [
            'title' => $this->advert->e_provider->name,
            'body' => trans('lang.notification_new_advert', ['advert_id' => $this->advert->id, 'user_name' => $this->advert->user->name]),
            'icon' => $this->geMediaUrl(),
            'click_action' => "FLUTTER_NOTIFICATION_CLICK",
            'id' => 'App\\Notifications\\NewAdvert',
            'status' => 'done',
        ];
        $data = $notification;
        $data['advertId'] = $this->advert->id;
        $message->content($notification)->data($data)->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }

    private function geMediaUrl(): string
    {
        if ($this->advert->getHasMediaAttribute()) {
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
    public function toArray($notifiable): array
    {
        return [
            'advert_id' => $this->advert['id'],
        ];
    }
}
