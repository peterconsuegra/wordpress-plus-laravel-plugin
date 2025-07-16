<?php


Route::get('wordpress_plus_laravel/create', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@create')->middleware(['web']);

Route::post('wordpress_plus_laravel/store', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@store')->middleware(['web']);

Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@index')->middleware(['web']);

Route::get('/wordpress_plus_laravel/{id}/edit', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@edit')->middleware(['web']);

Route::get('/wordpress_plus_laravel/logs/{id}', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@logs')->middleware(['web']);

Route::post('/wordpress_plus_laravel/force_delete', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@force_delete')->middleware(['web']);

Route::post('wordpress_plus_laravel/delete', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@delete')->middleware(['web']);

Route::get('/wordpress_plus_laravel/generate_ssl', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@wl_generate_ssl')->middleware(['web']);
