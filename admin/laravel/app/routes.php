<?php

/* Login */

Route::get('/','LoginController@loginStart');
Route::get('login','LoginController@loginStart');
Route::post('login','LoginController@loginSubmit');
Route::get('logout','LoginController@logout');

/* Dashboard */

Route::get('admin', 'AdminController@dashboard');
Route::get('dashboard', 'AdminController@dashboard');

/* Speed Screens */

Route::get('channel', 'ChannelController@index');
Route::get('channelSettings', 'ChannelController@settings');
Route::post('channelSettingsSubmit', 'ChannelController@settingsSubmit');
Route::post('channel/deploy','DeployController@deploy');
Route::get('speedScreen', 'ChannelController@speedScreen');
Route::post('channel/images/update', 'ChannelController@updateImage');
Route::post('channel/videos/update', 'ChannelController@updateVideo');
Route::post('channel/create', 'ChannelController@createChannel');

/* Booking */

Route::get('booking', 'BookingController@index');
Route::get('booking/settings', 'BookingController@settings');
Route::post('booking/settings/update', 'BookingController@updateSettings');
Route::post('booking/payments/settings/update', 'BookingController@updatePaymentSettings');
Route::post('booking/images/update', 'BookingController@updateImage');
Route::post('booking/files/update', 'BookingController@updateFile');
Route::get('booking/payments', 'BookingController@payments');
Route::get('booking/translations', 'BookingController@translations');
Route::post('booking/translations/update', 'BookingController@updateTranslations');
Route::get('booking/translations/update/culture/{cultureKey}', 'BookingController@updateCulture');
Route::get('booking/templates', 'BookingController@templates');
Route::post('booking/templates', 'BookingController@updateTemplates');
Route::get('booking/giftcardsales', 'BookingController@giftCardSales');
Route::post('booking/giftcardsales/update', 'BookingController@updateGiftCardSalesSettings');

/* iPad Registration */

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

Route::get('reports/broker-codes', 'BrokerCodesReportController@index');
Route::post('reports/broker-codes', 'BrokerCodesReportController@index');
Route::get('reports/broker-codes/export/csv', 'BrokerCodesReportController@exportToCSV');

Route::get('reports/detailed-sales', 'DetailedSalesReportController@index');
Route::post('reports/detailed-sales', 'DetailedSalesReportController@index');
Route::get('reports/detailed-sales/export/csv', 'DetailedSalesReportController@exportToCSV');

Route::get('reports/eurekas-payments', 'EurekasPaymentsReportController@index');
Route::post('reports/eurekas-payments', 'EurekasPaymentsReportController@index');
Route::get('reports/eurekas-payments/export/csv', 'EurekasPaymentsReportController@exportToCSV');

Route::get('reports/eurekas-detailed-sales', 'EurekasDetailedSalesReportController@index');
Route::post('reports/eurekas-detailed-sales', 'EurekasDetailedSalesReportController@index');
Route::get('reports/eurekas-detailed-sales/export/csv', 'EurekasDetailedSalesReportController@exportToCSV');

Route::get('reports/eurekas-summary-payments', 'EurekasSummaryPaymentsReportController@index');
Route::post('reports/eurekas-summary-payments', 'EurekasSummaryPaymentsReportController@index');
Route::get('reports/eurekas-summary-payments/export/csv', 'EurekasSummaryPaymentsReportController@exportToCSV');

/* Mobile */

Route::get('mobileApp/menuItems', 'MobileAppController@menuItems');
Route::post('mobileApp/images/update', 'MobileAppController@updateImage');
//Route::post('mobileApp/updateMenuItems', 'MobileAppController@updateMenuItems');
Route::get('mobileApp/templates', 'MobileAppController@templates');
Route::post('mobileApp/templates', 'MobileAppController@updateTemplates');
Route::get('mobileApp/settings', 'MobileAppController@settings');
Route::post('mobileApp/settings/update', 'MobileAppController@updateSettings');

/* Gift Cards */

Route::get('giftcards/manage', 'GiftCardsController@index');
Route::post('giftcards/balance/update', 'GiftCardsController@updateBalance');
Route::get('giftcards/reports', 'GiftCardsController@reports');
Route::get('giftcards/reports/balance', 'GiftCardsController@balanceReport');
Route::post('giftcards/reports/balance', 'GiftCardsController@getBalanceReport');
Route::get('giftcards/reports/balance/csv', 'GiftCardsController@getBalanceReportCSV');
Route::get('giftcards/reports/transactions', 'GiftCardsController@transactionReport');
Route::post('giftcards/reports/transactions', 'GiftCardsController@getTransactionReport');
Route::get('giftcards/reports/transactions/csv', 'GiftCardsController@getTransactionReportCSV');

/* Disconnected */

Route::get('/disconnected', 'DisconnectedController@entry');
