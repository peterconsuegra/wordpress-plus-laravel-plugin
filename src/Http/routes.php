<?php

Route::get('todo/list', 'Pete\WordPressPlusLaravel\Http\TodoController@getUserTodoList');
Route::get('wordpress_plus_laravel/create', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@create');

Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@index');

//Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@hello_wordpress_plus_laravel');