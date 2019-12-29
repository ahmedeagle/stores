<?php

Route::get('/', function () {
	return view('home');
});

Route::get('hyperpay', function () {

    $url = "https://test.oppwa.com/v1/checkouts";
    $data = "entityId=8a8294174d0595bb014d05d82e5b01d2" .
        "&amount=92.00" .
        "&currency=SAR" .
        "&paymentType=DB" .
        "&notificationUrl=http://www.example.com/notify";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization:Bearer OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $responseData;

});

Route::group(['middleware' => ['api_auth', 'CheckPhone'], 'prefix' => 'api'], function () {

    //////////////////tickets apis //////////////////

    Route::post("/GetTicketTypes", "TicketController@get_ticket_types");
    Route::get("/GetTicketTypes", "ProviderController@echo_Empty");

    Route::post("/AddTicket", "TicketController@add_ticket");
    Route::get("/AddTicket", "ProviderController@echo_Empty");

    Route::post("/GetTickets", "TicketController@get_tickets");
    Route::get("/GetTickets", "ProviderController@echo_Empty");

    Route::post("/GetTicketMessages", "TicketController@get_ticket_messages");
    Route::get("/GetTicketMessages", "ProviderController@echo_Empty");

    Route::post("/AddMessage", "TicketController@add_message");
    Route::get("/AddMessage", "ProviderController@echo_Empty");

//////////////////////////////// pages apis ////////////////////////////////////////////

    Route::post("/GetPages", "PageController@get_pages");
    Route::get("/GetPages", "ProviderController@echo_Empty");

    Route::post("/GetPage", "PageController@get_page");
    Route::get("/GetPage", "ProviderController@echo_Empty");

    Route::post('/UsageAgreement', 'PageController@get_usage_agreement_page');
    Route::get('/UsageAgreement', 'ProviderController@echoEmpty');

    ////////////////////////////// cities and countries apis /////////////////////////////

    Route::post('/countries', 'UserController@getCountries');
    Route::get('/countries', 'UserController@echoEmpty');
    Route::post('/countryCities', 'UserController@countryCityies');
    Route::get('/countryCities', 'UserController@echoEmpty');
    Route::post('/cities', 'UserController@cities');
    Route::get('/cities', 'UserController@echoEmpty');

    //////////////////////////////////// notification settings ///////////////////////////

    Route::post('/notificationSettings', 'NotificationController@getNotificationSettings');
    Route::get('/notificationSettings', 'ProviderController@echoEmpty');

    Route::post('/savenotificationSettings', 'NotificationController@saveNotificationSettings');
    Route::get('/savenotificationSettings', 'ProviderController@echoEmpty');

    Route::post('/get_notifications', 'NotificationController@get_notifications');
    Route::get('/get_notifications', 'ProviderController@echoEmpty');

    Route::post('/GetUnreadNotificationsCount', 'NotificationController@UnreadNotifications');
    Route::get('/GetUnreadNotificationsCount', 'ProviderController@echoEmpty');

    Route::post('/withdraw_request', 'ProviderController@withdraw');
    Route::get('/withdraw_request', 'ProviderController@echoEmpty');

    Route::post('/addCheckoutPaid', 'ProviderController@addCheckoutPaid');
    Route::get('/addCheckoutPaid', 'ProviderController@echoEmpty');

    Route::post('/changeAppLanguage', 'ProviderController@changeAppLanguage');
    Route::get('/changeAppLanguage', 'ProviderController@echoEmpty');

    Route::post('/logout', 'ProviderController@logout');
    Route::get('/logout', 'ProviderController@echoEmpty');

});
