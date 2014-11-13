<?php

Route::get('/','LoginController@loginStart');
Route::get('login','LoginController@loginStart');
Route::post('login','LoginController@loginSubmit');
Route::get('logout','LoginController@logout');

Route::get('admin', 'AdminController@dashboard');
Route::get('dashboard', 'AdminController@dashboard');
Route::get('channel', 'ChannelController@index');
Route::get('channelSettings', 'ChannelController@settings');
Route::post('channelSettingsSubmit', 'ChannelController@settingsSubmit');
Route::post('channel/deploy','DeployController@deploy');

Route::get('booking', 'BookingController@index');
Route::get('booking/settings', 'BookingController@settings');
Route::post('booking/settings/update', 'BookingController@updateSettings');
Route::post('booking/payments/settings/update', 'BookingController@updatePaymentSettings');

Route::get('booking/payments', 'BookingController@payments');

Route::get('booking/emailTemplates', 'BookingController@emailTemplates');
Route::post('booking/emailTemplates', 'BookingController@updateEmailTemplates');


Route::get('registration/settings', 'RegistrationController@settings');
Route::post('registration/settings/update', 'RegistrationController@updateSettings');

Route::get('/disconnected', 'DisconnectedController@entry');