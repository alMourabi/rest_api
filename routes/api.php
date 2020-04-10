<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
Route::post('email/password_reset', 'AuthController@resetPassword')->name('password.reset');
Route::post('email/set_password', 'AuthController@setPassword')->name('password.change');
Route::get('classe', 'ClasseController@index');
Route::get('/sjfkjdsf', function () {
    // dd(Config::get('database.connections.mysql.database'));
    Artisan::call('migrate');
    dd(Artisan::output());
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::get('user/{user}', 'AuthController@getUser');
        Route::post('user/{user}/subscribe', 'AuthController@subscribe');
        Route::post('user/{user}/unsubscribe', 'AuthController@unsubscribe');
        Route::get('users', 'AuthController@users');
        Route::post('password_check', 'AuthController@checkPassword');
        Route::post('password_update', 'AuthController@updatePassword');
        Route::put('user/{user}', 'AuthController@updateUser');
    });
});

Route::get('test', 'ClassPDFController@test');
Route::get('videos/{video}', 'VideoController@download');

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('class_picture', 'ClassPictureController');
    Route::resource('class_pdf', 'ClassPDFController');
    Route::resource('classe', 'ClasseController');
    Route::resource('subject', 'SubjectController');
    Route::resource('video', 'VideoController');
    Route::resource('rating', 'RatingController');
    Route::resource('comment', 'CommentController');
    Route::post('link', 'SubjectController@link');
    Route::delete('link', 'SubjectController@unlink');
    Route::post('code/add', 'CodeController@store');
    Route::post('code/submit', 'CodeController@verify');
    Route::post('code', 'CodeController@index');
    Route::get('media/{image}', function ($image) {
        dd(Storage::get($image));
        return Response::stream(function () {
            $filename = $image;
            readfile($filename);
        }, 200, ['content-type' => 'image/jpeg']);
    });
});
