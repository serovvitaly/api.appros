<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::controller('tours', 'ToursController');

Route::controller('admin', 'Admin\AdminController');

Route::controller('expedia', 'ExpediaController');

Route::get('/', function()
{
	return View::make('hello');
});

//Route::get('tours', array('uses' => 'ToursController'));

Route::controller('call', 'CallController');
