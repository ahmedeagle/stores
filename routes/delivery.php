<?php

/*
/
 Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 


Route::group(['middleware' => ['api_auth'], 'prefix' => 'api'], function() {
            
            Route::post('/deliveryPrepareSignUp', 'DeliveryController@prepareSignUp');
	        Route::get('/deliveryPrepareSignUp', 'DeliveryController@echoEmpty');

	        Route::post('/deliverySignUp', 'DeliveryController@signUp');
	        Route::get('/deliverySignUp', 'DeliveryController@echoEmpty');

	        Route::post('/deliveryActivation', 'DeliveryController@activateDelivery');
	        Route::get('/deliveryActivation', 'DeliveryController@echoEmpty');

        	Route::post('/resendDeliveryActivationCode','DeliveryController@resendActivationCode');
            Route::get('/resendDeliveryActivationCode','DeliveryController@echoEmpty');

            Route::post('/deliveryLogin','DeliveryController@deliveryLogin');
            Route::get('/deliveryLogin','DeliveryController@echoEmpty');

            Route::post('/deliveryForgetPassword','DeliveryController@forgetPassword');
            Route::get('/deliveryForgetPassword','DeliveryController@echoEmpty');

            Route::post('/deliveryUpdatePassword', 'DeliveryController@updatePassword');
         	Route::get('/deliveryUpdatePassword', 'DeliveryController@echoEmpty');

         	Route::post('/editDeliveryProfile', 'DeliveryController@editProfile');
         	Route::get('/editDeliveryProfile', 'DeliveryController@echoEmpty');

         	Route::post('/updateDelivery', 'DeliveryController@updateProfile');
         	Route::get('/updateDelivery', 'DeliveryController@echoEmpty');

         	Route::post('/newOrders', 'DeliveryController@newOrders');
         	Route::get('/newOrders', 'DeliveryController@echoEmpty');

         	Route::post('/deliveryOrderDetails', 'DeliveryController@OrderDetails');
         	Route::get('/deliveryOrderDetails', 'DeliveryController@echoEmpty');

         	Route::post('/deliveryChangeOrderStatus', 'DeliveryController@changeOrderStatus');
         	Route::get('/deliveryChangeOrderStatus', 'DeliveryController@echoEmpty');

         	Route::post('/deliveryPrepareSearch', 'DeliveryController@prepareSearch');
			Route::get('/deliveryPrepareSearch', 'DeliveryController@echoEmpty');

         	Route::post('/deliverySearch', 'DeliveryController@search');
			Route::get('/deliverySearch', 'DeliveryController@echoEmpty');

			
         	

         	

             
             

    });
 