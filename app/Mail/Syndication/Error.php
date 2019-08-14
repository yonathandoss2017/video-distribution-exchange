<?php

namespace App\Mail\Syndication;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Error extends Mailable
{
    use Queueable, SerializesModels;

    public $errMessage;
    public $platformType;
    public $platformId;
    public $entry;
    public $feed;

    /**
     * Create a new message instance.
     */
    public function __construct($errMessage, $platformType, $platformId, $entry, $feed)
    {
        $this->errMessage = $errMessage;
        $this->platformType = $platformType;
        $this->platformId = $platformId;
        $this->entry = $entry;
        $this->feed = $feed;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Syndication Error')
            ->view('emails.syndication.error')
            ->with([
                'website' => $this->feed->postFeedConfiguration->website,
                'syndicationUrl' => route('manage.sp.syndication.edit', [$this->feed->property_id, $this->feed->id]),
            ]);
    }
}
