<?php

namespace Amitav\Todo;

use Illuminate\Support\ServiceProvider;

class TodoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('todo', function ($app) {
            return new Todo;
        });
    }

    public function boot()
    {
        // loading the routes file
        require __DIR__ . '/Http/routes.php';
		
		//define the path for the view files
		$this->loadViewsFrom(__DIR__.'/../views','todo');
		
		//define files which are going to publish
		$this->publishes([__DIR__.'/migrations/2020_05_000000_create_todo_table.php' => base_path('database/migrations/2020_05_000000_create_to_table.php')]);
		
    }
}
