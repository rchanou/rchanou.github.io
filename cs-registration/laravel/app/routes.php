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

Route::get('/','Step1Controller@step1');
Route::get('/step1','Step1Controller@step1');
Route::get('/step2','Step2Controller@step2');
Route::post('/step2','Step2Controller@postStep2');
Route::get('/step3','Step3Controller@step3');
Route::post('/step3','Step3Controller@postStep3');
Route::get('/step4','Step4Controller@step4');
Route::get('/disconnected','DisconnectedController@disconnected');

Route::get('/changeLanguage/{newLanguageCode}/{destinationStep}','LocalizationController@changeLanguage');