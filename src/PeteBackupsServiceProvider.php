<?php

namespace Pete\PeteBackups;

use Illuminate\Support\ServiceProvider;

class PeteBackupsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('pete-backups-plugin', function ($app) {
            return new PeteBackups;
        });	
    }

    public function boot()
    {
		
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/views', 'pete-backups-plugin');
		
    }
}
