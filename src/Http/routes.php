<?php


Route::get('wordpress_backups', 'Pete\PeteBackups\Http\PeteBackupsController@index');
Route::post('wordpress_backups/create', 'Pete\PeteBackups\Http\PeteBackupsController@create');
Route::post('wordpress_backups/restore', 'Pete\PeteBackups\Http\PeteBackupsController@restore');
Route::post('wordpress_backups/destroy', 'Pete\PeteBackups\Http\PeteBackupsController@destroy');
