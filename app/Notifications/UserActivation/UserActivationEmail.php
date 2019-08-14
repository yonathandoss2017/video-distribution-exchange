<?php

namespace App\Notifications\UserActivation;

use Illuminate\Notifications\Messages\MailMessage;

class UserActivationEmail extends BaseActivationEmail
{
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
            ->subject(__('email/user/userActivation.subject', [], $this->locale))
            ->greeting(__('email/user/userActivation.greeting', ['user' => strtok($this->user->name, '')], $this->locale))
            ->line(__('email/user/userActivation.introLines', [], $this->locale))
            ->action(__('email/user/userActivation.action', [], $this->locale), url('activate', $this->token))
            ->line(__('email/user/userActivation.outroLines', [], $this->locale))
            ->line($this->locale);
    }
}
