<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'list app names';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('ucastx');
        $this->info('cnc');
        $this->info('hubei');
    }
}
