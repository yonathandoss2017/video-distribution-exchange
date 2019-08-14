<?php

namespace App\Notifications\Dpp;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $organization;
    public $property;
    public $playlist;
    public $locale;

    /**
     * Create a new notification instance.
     */
    public function __construct($organization, $property, $playlist, $locale)
    {
        $this->organization = $organization;
        $this->property = $property;
        $this->playlist = $playlist;
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
            ->subject(__('email/dpp/review.subject', ['playlistName' => $this->playlist->name], $this->locale))
            ->greeting(__('email/dpp/review.greeting', ['users' => explode(' ', $notifiable->name)[0]], $this->locale))
            ->line(__('email/dpp/review.introLines', ['playlistName' => $this->playlist->name], $this->locale))
            ->action(__('email/dpp/review.action', [], $this->locale), url('manage/'.$this->property->id.'/cp/dpp/'.$this->playlist->id.'/edit'))
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
