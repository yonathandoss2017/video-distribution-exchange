<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\PropertyCP;
use App\Models\RequestLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\LicensingTermNotification;

class SendLicensingRequestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $playlistIds;
    private $requestLog;
    private $sender;
    private $locale;

    public function __construct(RequestLog $requestLog, array $playlistIds, User $sender, $locale)
    {
        $this->playlistIds = $playlistIds;
        $this->requestLog = $requestLog;
        $this->sender = $sender;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('sending licensing request emails');

        $playlistIds = $this->playlistIds;
        $contentProviders = PropertyCP::whereHas('playlists', function ($query) use ($playlistIds) {
            $query->whereIn('id', $playlistIds);
        })->with(['license_notifications' => function ($query) {
            $query->where('status', 1);
        }])
            ->get();

        $users = [];

        $contentProviders->each(function ($contentProvider) use (&$users) {
            $contentProvider->license_notifications->each(function ($licenseNotification) use (&$users, $contentProvider) {
                $licenseNotification->contentProvider = $contentProvider;
                $users[] = $licenseNotification;
            });
        });

        $users = collect($users);

        $userArray = $users->keyBy('id')->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
            ];
        })->toArray();

        $this->requestLog->users()->attach($userArray);

        foreach ($users as $user) {
            $user->notify(new LicensingTermNotification($this->requestLog, $user, $user->contentProvider, $this->sender, $this->locale));
        }
    }
}
