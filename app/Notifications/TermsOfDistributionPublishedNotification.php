<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\PropertySP;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TermsOfDistributionPublishedNotification extends Notification
{
    use Queueable;

    public $user;
    public $property;
    public $locale;

    public function __construct(User $user, PropertySP $property, $locale)
    {
        $this->user = $user;
        $this->property = $property;
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
                    ->subject(__('email/exchange/todPublished.subject', [], $this->locale))
                    ->greeting(__('email/exchange/todPublished.greeting', ['user' => $this->user->name], $this->locale))
                    ->line(__('email/exchange/todPublished.introLines', [], $this->locale))
                    ->action(__('email/exchange/todPublished.action', [], $this->locale), route('manage.sp.tod.index', [
                        'property' => $this->property,
                        'status' => 'pending',
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
