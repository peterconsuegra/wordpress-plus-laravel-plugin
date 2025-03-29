<?php


Route::get('wordpress_plus_laravel/create', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@create')->middleware(['web']);

Route::post('wordpress_plus_laravel/store', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@store')->middleware(['web']);

Route::get('wordpress_plus_laravel', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@index')->middleware(['web']);

Route::get('wordpress_plus_laravel/trash', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@trash')->middleware(['web']);

Route::get('wordpress_plus_laravel/restore', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@restore')->middleware(['web']);

Route::get('/wordpress_plus_laravel/{id}/edit', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@edit')->middleware(['web']);

Route::post('/wordpress_plus_laravel/force_delete', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@force_delete')->middleware(['web']);

Route::post('wordpress_plus_laravel/destroy', 'Pete\WordPressPlusLaravel\Http\WordPressPlusLaravelController@destroy')->middleware(['web']);
