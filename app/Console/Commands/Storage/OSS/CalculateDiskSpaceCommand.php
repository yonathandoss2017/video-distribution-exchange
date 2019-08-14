<?php

namespace App\Console\Commands\Storage\OSS;

use App\Models\Organization;
use Illuminate\Console\Command;
use App\Jobs\Storage\OSS\CalculateDiskSpaceJob;

class CalculateDiskSpaceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:getSize {organization} {dirPath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the size of all files in each organization on Oss.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $organization = $this->argument('organization');
        if (!empty($organization)) {
            $organizations = Organization::whereIn('id', explode(',', $organization))->get();
        } else {
            $organizations = Organization::all();
        }
        //the dirPath should be the last level directory.
        $dirPath = $this->argument('dirPath');

        foreach ($organizations as $organization) {
            if (empty($dirPath)) {
                $organization->storage_size_in_byte = 0;
                $organization->save();
                $prefix = $organization->id.'/';
            } else {
                if (!ends_with($dirPath, '/')) {
                    $prefix = $dirPath.'/';
                } else {
                    $prefix = $dirPath;
                }
            }
            dispatch(new CalculateDiskSpaceJob($organization->id, $prefix));
        }
    }
}
