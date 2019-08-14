<?php

namespace App\Notifications\UserActivation;

use Illuminate\Notifications\Messages\MailMessage;

class UserActivationWithPassResetEmail extends BaseActivationEmail
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
            ->subject(__('email/user/userActivationWithPassReset.subject', [], $this->locale))
            ->greeting(__('email/user/userActivationWithPassReset.greeting', ['user' => strtok($this->user->name, '')], $this->locale))
            ->line(__('email/user/userActivationWithPassReset.introLines', [], $this->locale))
            ->action(__('email/user/userActivationWithPassReset.action', [], $this->locale), url('password/activate', [urlencode($this->user->email), $this->token]))
            ->line(__('email/user/userActivationWithPassReset.outroLines', [], $this->locale))
            ->line($this->locale);
    }
}
