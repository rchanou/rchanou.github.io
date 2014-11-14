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

Route::get('booking/templates', 'BookingController@templates');
Route::post('booking/templates', 'BookingController@updateTemplates');

Route::get('registration/settings', 'RegistrationController@settings');
Route::post('registration/settings/update', 'RegistrationController@updateSettings');

/* Reports */

Route::get('reports', 'ReportsController@index');
Route::get('reports/payments', 'PaymentsReportController@index');
Route::post('reports/payments', 'PaymentsReportController@index');
Route::get('reports/payments/export/csv', 'PaymentsReportController@exportToCSV');

Route::get('reports/summary-payments', 'SummaryPaymentsReportController@index');
Route::post('reports/summary-payments', 'SummaryPaymentsReportController@index');
Route::get('reports/summary-payments/export/csv', 'SummaryPaymentsReportController@exportToCSV');

Route::get('reports/detailed-sales', 'DetailedSalesReportController@index');
Route::post('reports/detailed-sales', 'DetailedSalesReportController@index');
Route::get('reports/detailed-sales/export/csv', 'DetailedSalesReportController@exportToCSV');

/* End Reports */

Route::get('/disconnected', 'DisconnectedController@entry');