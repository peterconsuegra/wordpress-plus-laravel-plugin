<?php

namespace Pete\WordPressPlusLaravel;

use Illuminate\Support\ServiceProvider;

class WordPressPlusLaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('wordpress-plus-laravel', function ($app) {
            return new WordPressPlusLaravel;
        });
    }

    public function boot()
    {
        // loading the routes file
        require __DIR__ . '/Http/routes.php';
		
		//define the path for the view files
		$this->loadViewsFrom(__DIR__.'/../views','wordpress-plus-laravel-plugin');
		
		//define files which are going to publish
		//$this->publishes([__DIR__.'/migrations/2020_05_000000_create_todo_table.php' => base_path('database/migrations/2020_05_000000_create_to_table.php')]);
		
		$this->publishes([__DIR__.'/scripts/unix_wordpress_laravel.sh' => base_path('scripts/unix_wordpress_laravel.sh')]);
		
    }
}
