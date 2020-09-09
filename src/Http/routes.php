<?php


Route::get('wordpress_plus_laravel/create', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@create');

Route::post('wordpress_plus_laravel/store', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@store');

Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@index');

Route::get('/wordpress_plus_laravel/{id}/edit', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@edit');

