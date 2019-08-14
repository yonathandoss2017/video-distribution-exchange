<?php

namespace App\Console\Commands\Tools;

use Illuminate\Console\Command;
use App\Services\Tools\PhpCsFixerService;

class CodeFixRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codefix:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix code styling';

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
        $errors = collect(PhpCsFixerService::check(true))->map(function ($error) {
            return [$error];
        })->toArray();

        $statusCode = 0;
        if ($errors) {
            $statusCode = 1;
        }

        if (0 !== $statusCode) {
            $this->error('style fixed');
            $this->info(count($errors).' files updated');
            $this->table(['filename'], $errors);

            return $statusCode;
        }

        $this->info('all checks passed!');

        return 0;
    }
}
