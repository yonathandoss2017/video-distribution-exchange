<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Solr\DumpDataToSolrCommand::class,
        \App\Console\Commands\Solr\PingSolrCommand::class,
        \App\Console\Commands\Solr\CommitSolrCommand::class,
        \App\Console\Commands\Solr\ResetSolrCommand::class,
        \App\Console\Commands\Entry\DeleteEntryCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('solr:sync')->everyFiveMinutes()->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
