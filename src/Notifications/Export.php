<?php

namespace Joy\VoyagerExport\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Export extends Notification
{
    use Queueable;

    public $path;

    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        $path,
        $url
    ) {
        $this->path = $path;
        $this->url  = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('joy-voyager-export.notification_via', ['mail', 'database']);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed                                          $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Your export is ready!')
            ->action('Download', $this->url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'path' => $this->path,
            'url'  => $this->url,
        ];
    }
}
