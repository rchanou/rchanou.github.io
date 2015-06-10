<?php


Route::get('/',function() {return Redirect::to('/step1'); });
Route::get('/step1','Step1Controller@entry');
Route::get('/step2','Step2Controller@entry');
Route::post('/step2','Step2Controller@entry');

Route::get('/giftcards','GiftCardsController@entry');

Route::get('/login','LoginController@loginEntry');
Route::post('/login','LoginController@login');
Route::get('/loginfb','LoginController@loginFacebook');
Route::post('/loginfbconfirm','LoginController@loginFacebookConfirm');


Route::post('/createaccount','CreateAccountController@entry');
Route::get('/cart','CartController@entry');
Route::post('/cart','CartController@entry');
Route::post('/cart/brokername','CartController@applyBrokerName');

Route::get('/checkout','CheckoutController@entry');
Route::post('/pay','CheckoutController@pay');
Route::get('/success','SuccessController@success');


Route::get('/resetpassword','ResetPasswordController@entry');
Route::post('/resetpassword','ResetPasswordController@resetPasswordRequest');
Route::get('/resetpassword/form','ResetPasswordController@resetPasswordForm');
Route::post('/resetpassword/form','ResetPasswordController@resetPasswordSubmission');

Route::get('/resetpassword/form/ios','ResetPasswordController@resetPasswordiOS');
Route::get('/resetpassword/form/android','ResetPasswordController@resetPasswordAndroid');

Route::get('/logout','LogoutController@entry');

Route::get('/disconnected', 'DisconnectedController@entry');
Route::get('/disabled', 'DisabledController@entry');

Route::get('/changeLanguage/{newLanguageCode}/{destination}','LocalizationController@changeLanguage');