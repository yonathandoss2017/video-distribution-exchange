<?php

namespace App\Console\Commands\Entry;

use App\Models\Entry;
use App\Jobs\EntryDeletionJob;
use Illuminate\Console\Command;

class DeleteEntryCommand extends Command
{
    protected $signature = 'entry:delete {--i|entry_ids=}';
    protected $description = 'Delete entries';

    public function __construct(\Solarium\Client $client)
    {
        parent::__construct();
    }

    public function handle()
    {
        $entryIds = $this->option('entry_ids');
        if (empty($entryIds)) {
            $this->info('entry_ids is required!');
            exit(-1);
        }

        $entryIds = explode(',', $entryIds);
        dispatch(new EntryDeletionJob($entryIds));

        $entries = Entry::whereIn('id', $entryIds)
            ->get();
        $count = $entries->count();

        $this->info(PHP_EOL."$count entries will be deleted");
    }
}
