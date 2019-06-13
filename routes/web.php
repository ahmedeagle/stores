<?php

Route::group(['middleware' => ['api_auth'], 'prefix' => 'api'], function() {

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
 
        


    Route::post('/notificationSettings', 'ProviderController@getNotificationSettings');
    Route::get('/notificationSettings', 'ProviderController@echoEmpty');

    Route::post('/savenotificationSettings', 'ProviderController@saveNotificationSettings');
    Route::get('/savenotificationSettings', 'ProviderController@echoEmpty');

    Route::post('/get_notifications', 'NotificationController@get_notifications');
    Route::get('/get_notifications', 'ProviderController@echoEmpty');

    Route::post('/GetUnreadNotificationsCount', 'NotificationController@UnreadNotifications');
    Route::get('/GetUnreadNotificationsCount', 'ProviderController@echoEmpty');
  

    Route::post('/withdraw_request', 'ProviderController@withdraw');
    Route::get('/withdraw_request', 'ProviderController@echoEmpty');

});
