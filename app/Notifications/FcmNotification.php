<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FcmNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['fcm'];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): CloudMessage
    {
        $firebaseNotification = FirebaseNotification::create($this->title, $this->body);

        $message = CloudMessage::new()
            ->withNotification($firebaseNotification)
            ->withData($this->data);

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
