<?php

namespace App\Notifications\Exchange;

use Illuminate\Bus\Queueable;
use App\Models\TermsOfDistribution;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SpDeclineTodEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $tod;
    private $sp;
    private $cp;
    private $locale;

    /**
     * Create a new notification instance.
     */
    public function __construct($tod, $sp, $cp, $locale)
    {
        $this->tod = $tod;
        $this->sp = $sp;
        $this->cp = $cp;
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
                    ->subject(__('email/exchange/spDeclineTod.subject', [], $this->locale))
                    ->greeting(__('email/exchange/spDeclineTod.greeting', ['users' => explode(' ', $notifiable->name)[0]], $this->locale))
                    ->line(__('email/exchange/spDeclineTod.introLines', ['todName' => $this->tod->name, 'spName' => $this->sp->name], $this->locale))
                    ->action(__('email/exchange/spDeclineTod.action', [], $this->locale), route('manage.cp.exchange.distribution.index', [
                        'property' => $this->cp->id,
                        'status' => TermsOfDistribution::STATUS_SP_DECLINED,
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
