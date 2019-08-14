<?php

namespace App\Notifications\Exchange;

use App\Models\PropertySP;
use Illuminate\Bus\Queueable;
use App\Models\TermsOfDistribution;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SpAcceptTodEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $property;
    private $tod;
    private $locale;

    public function __construct(PropertySP $property, TermsOfDistribution $tod, $locale)
    {
        $this->property = $property;
        $this->tod = $tod;
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
                    ->subject(__('email/exchange/spAcceptTod.subject', [], $this->locale))
                    ->greeting(__('email/exchange/spAcceptTod.greeting', ['user' => $notifiable->name], $this->locale))
                    ->line(__('email/exchange/spAcceptTod.introLines', ['todName' => $this->tod->name, 'spName' => $this->property->name], $this->locale))
                    ->action(__('email/exchange/spAcceptTod.action', [], $this->locale), route('manage.cp.exchange.distribution.show', [
                        'property' => $this->tod->cp_property_id,
                        'distribution' => $this->tod->id,
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
