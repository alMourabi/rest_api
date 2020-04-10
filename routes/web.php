<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::group(['middleware' => 'auth:api'], function () {

// Route::get('storage/pdfs/{image}', function ($image) {
// 	// exec('cp -rf /home/almourb/www/api/storage/app/public/pdfs/ /home/almourb/www/api/storage/app/');
// 	$image = '/home/almourb/www/api/storage/app/pdfs/'.$image;
// 	return response()->download($image);
// });

// });

Auth::routes();
