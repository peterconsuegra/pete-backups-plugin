<?php


Route::get('wordpress_backups', 'Pete\PeteBackups\Http\PeteBackupsController@index')->middleware(['web']);
Route::get('wordpress_backups/create', 'Pete\PeteBackups\Http\PeteBackupsController@create')->middleware(['web']);
Route::get('wordpress_backups/restore', 'Pete\PeteBackups\Http\PeteBackupsController@restore')->middleware(['web']);
Route::post('wordpress_backups/destroy', 'Pete\PeteBackups\Http\PeteBackupsController@destroy')->middleware(['web']);
