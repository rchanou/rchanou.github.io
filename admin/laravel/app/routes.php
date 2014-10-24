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

Route::get('/disconnected', 'DisconnectedController@entry');