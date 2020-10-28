<?php

namespace Pete\PeteBackups;

use Illuminate\Support\ServiceProvider;

class PeteBackupsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('pete-backups-plugin', function ($app) {
            return new PeteBackups;
        });
    }

    public function boot()
    {
        // loading the routes file
        require __DIR__ . '/Http/routes.php';
		
		//define the path for the view files
		$this->loadViewsFrom(__DIR__.'/../views','pete-backups-plugin');
		
		//define files which are going to publish
		//$this->publishes([__DIR__.'/migrations/2020_05_000000_create_todo_table.php' => base_path('database/migrations/2020_05_000000_create_to_table.php')]);
		
		//$this->publishes([__DIR__.'/scripts/mac_wordpress_laravel.sh' => base_path('scripts/mac_wordpress_laravel.sh')]);
		
    }
}
