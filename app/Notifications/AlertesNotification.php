<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertesNotification extends Notification
{
    use Queueable;
    private $notifs;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notifs)
    {
        $this->notifs = $notifs;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'u_name'     => $this->notifs['u_name'],
            'u_id'     => $this->notifs['u_id'],
            'data'       => $this->notifs['body'],
            'infos_user' => $this->notifs['infos_user'],
            'elem_id'    => $this->notifs['elem_id'],
        ];
    }
}
