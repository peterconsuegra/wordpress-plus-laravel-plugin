<?php

namespace Pete\WordPressPlusLaravel;

use Illuminate\Support\ServiceProvider;

class WordPressPlusLaravelServiceProvider extends ServiceProvider
{
    public function register()
    {	
        $this->app->singleton('wordpress-plus-laravel', function ($app) {
            return new WordPressPlusLaravel;
        });	
    }

    public function boot()
    {
        
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/views', 'wordpress-plus-laravel-plugin');
		
        if ($this->app->runningInConsole()) {
            $this->commands([
				Console\AdaptWordPressPlusLaravel::class,
            ]);
        }
		
    }
}
