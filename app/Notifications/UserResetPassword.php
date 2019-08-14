<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $name;
    public $email;
    public $locale;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email, $name, $locale)
    {
        $this->token = $token;
        $this->name = $name;
        $this->email = $email;
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
        return (new MailMessage())
                    ->subject(__('email/user/userResetPwd.subject', [], $this->locale))
                    ->greeting(__('email/user/userResetPwd.greeting', ['user' => strtok($this->name, '')], $this->locale))
                    ->line(__('email/user/userResetPwd.introLines', [], $this->locale))
                    ->action(__('email/user/userResetPwd.action', [], $this->locale), url('password/reset', [urlencode($this->email), $this->token]))
                    ->line(__('email/user/userResetPwd.outroLines', [], $this->locale))
                    ->line($this->locale);
    }
}
