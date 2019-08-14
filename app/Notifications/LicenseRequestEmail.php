<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LicenseRequestEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $name;
    protected $request_org;
    protected $request_property;
    protected $request_playlist;
    protected $review_url;
    protected $locale;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $request_organization_name
     * @param $request_property_name
     * @param $request_playlist_name
     */
    public function __construct($name, $request_organization_name, $request_property_name, $request_playlist_name, $review_url, $locale)
    {
        $this->name = $name;
        $this->request_org = $request_organization_name;
        $this->request_property = $request_property_name;
        $this->request_playlist = $request_playlist_name;
        $this->review_url = $review_url;
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
                    ->subject(__('email/license/licenseRequest.subject', [], $this->locale))
                    ->greeting(__('email/license/licenseRequest.greeting', ['user' => strtok($this->name, '')], $this->locale))
                    ->line(__('email/license/licenseRequest.introLines', ['requestOrg' => $this->request_org, 'playlist' => $this->request_playlist, 'property' => $this->request_property], $this->locale))
                    ->action(__('email/license/licenseRequest.action', [], $this->locale), $this->review_url)
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
