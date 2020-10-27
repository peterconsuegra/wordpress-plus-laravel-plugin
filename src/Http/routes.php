<?php


Route::get('wordpress_plus_laravel/create', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@create');

Route::post('wordpress_plus_laravel/store', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@store');

Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@index');

Route::get('wordpress_plus_laravel/trash', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@trash');

Route::get('/wordpress_plus_laravel/{id}/edit', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@edit');

Route::post('/wordpress_plus_laravel/force_delete', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@force_delete');

Route::post('wordpress_plus_laravel/destroy', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@destroy');

