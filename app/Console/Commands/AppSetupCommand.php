<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup {app_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'setup app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $app_name = $this->argument('app_name');
        if (!in_array($app_name, ['', 'ucastx', 'cnc'])) {
            $this->info('app name is invalid');

            return;
        }
        $config_app_name = config('app.name', 'ucastx');
        if ($app_name) {
            $path = base_path('.env');

            if (file_exists($path)) {
                if (file_put_contents($path, str_replace(
                    'APP_NAME='.$config_app_name, 'APP_NAME='.$app_name, file_get_contents($path)
                ))) {
                    $config_app_name = $app_name;
                }
            }
        }

        if (file_exists(resource_path('lang'))) {
            unlink(resource_path('lang'));
            $this->info('The old "resources/lang" directory has been removed.');
        }

        $this->laravel->make('files')->link(
            resource_path('lang_'.$config_app_name), resource_path('lang')
        );

        $this->info('The [resources/lang] directory has been linked.');

        if (file_exists(public_path('images/app'))) {
            unlink(public_path('images/app'));
            $this->info('The old "public/images/app" directory has been removed.');
        }

        $this->laravel->make('files')->link(
            public_path('images/'.$config_app_name), public_path('images/app')
        );

        $this->info('The [public/images/app] directory has been linked.');
    }
}
