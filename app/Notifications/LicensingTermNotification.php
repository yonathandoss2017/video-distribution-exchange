<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Property;
use App\Models\RequestLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LicensingTermNotification extends Notification
{
    use Queueable;

    public $requestLog;
    public $user;
    public $property;
    public $sender;
    public $locale;

    public function __construct(RequestLog $requestLog, User $user, Property $property, User $sender, $locale)
    {
        $this->requestLog = $requestLog;
        $this->user = $user;
        $this->property = $property;
        $this->sender = $sender;
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
                    ->greeting(__('email/license/licenseTerm.greeting', ['user' => $this->user->name], $this->locale))
                    ->subject($this->requestLog->subject)
                    ->line(__('email/license/licenseTerm.introLines', ['sender' => $this->sender->name], $this->locale))
                    ->action(__('email/license/licenseTerm.action', [], $this->locale), route('manage.cp.exchange.request_logs.show', [
                        'property' => $this->property,
                        'requestLog' => $this->requestLog,
                    ]))
                    ->line($this->locale);
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
