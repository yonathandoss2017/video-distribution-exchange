<?php

namespace App\Notifications\UserActivation;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaseActivationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $token;
    public $locale;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $locale)
    {
        $this->user = $user;
        $this->token = $user->token;
        $this->locale = $locale;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
