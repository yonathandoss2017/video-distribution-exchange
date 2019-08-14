<?php

namespace App\Console\Commands\Tools;

use Illuminate\Console\Command;
use App\Services\Tools\PhpCsFixerService;

class CodeFixDryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codefix:dry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check code styling';

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
        $errors = collect(PhpCsFixerService::check())->map(function ($error) {
            return [$error];
        })->toArray();

        $statusCode = 0;
        if ($errors) {
            $statusCode = 1;
        }

        if (0 !== $statusCode) {
            $this->error('check failed');
            $this->info('Please check php-cs-fixer style files below:');
            $this->table(['filename'], $errors);
            $this->info('fix styling by call codefix:run command');

            return $statusCode;
        }

        $this->info('all checks passed!');

        return 0;
    }
}
