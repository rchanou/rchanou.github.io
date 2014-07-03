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

Route::get('/',function() {return Redirect::to('/step1'); });
Route::get('/step1','RegistrationController@step1');
Route::get('/step2','RegistrationController@step2');
Route::post('/step2','RegistrationController@postStep2');
Route::get('/step3','RegistrationController@step3');
Route::post('/step3','RegistrationController@postStep3');
Route::get('/step4','RegistrationController@step4');
Route::get('/disconnected','RegistrationController@disconnected');

Route::get('/changeLanguage/{newLanguageCode}/{destinationStep}','RegistrationController@changeLanguage');