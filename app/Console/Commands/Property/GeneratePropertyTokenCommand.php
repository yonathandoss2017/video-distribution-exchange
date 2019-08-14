<?php

namespace App\Console\Commands\Property;

use App\Models\Property;
use Illuminate\Console\Command;
use App\Services\Property\PropertyService;

class GeneratePropertyTokenCommand extends Command
{
    const ERROR_ARGUMENT = 'error argument : property_id!';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'property:generate-token {property_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate property\'s api token';

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
        $property = Property::find($this->argument('property_id'));
        if (!$property) {
            $this->info(self::ERROR_ARGUMENT);

            return;
        }
        PropertyService::generateKeyAndToken($property);
        $this->info('api_key: '.$property->api_key);
        $this->info('api_token: '.$property->api_token);
    }
}
