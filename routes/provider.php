<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 

 
Route::get('/notfound', function(){
	return view('cpanel.404');
})->name('404');

Route::get('/', function (){
    return view('welcome');
});



Route::group(['middleware' => ['api_auth'], 'prefix' => 'api'], function() {
    
    
    	//crons
	Route::get('/crons', 'Crons@cron_job');
  
	   // prepare signup first step  provider personal account apis 
	Route::post('/getProviderSignUpFirstStep', 'ProviderController@prepareSignUp');
	Route::get('/getProviderSignUpFirstStep', 'ProviderController@echoEmpty');

     //store signup for first Step
	Route::post('/providerSignUpFirstStep', 'ProviderController@signUp');
	Route::get('/providerSignUpFirstStep', 'ProviderController@echoEmpty');

    Route::post('/invitationCode', 'ProviderController@getInvitationCode');
	Route::get('/invitationCode', 'ProviderController@echoEmpty');

    
    Route::post('/activateAccount', 'ProviderController@activateAccount');
	Route::get('/activateAccount', 'ProviderController@echoEmpty');

	Route::post('/resendActivationCode', 'ProviderController@resendActivationCode');
	Route::get('/resendActivationCode', 'ProviderController@echoEmpty');
 


  // prepare signup second step
	Route::post('/getProviderSignUpSecondStep', 'ProviderController@prepareSignUpSecondStep');
	Route::get('/getProviderSignUpSecondStep', 'ProviderController@echoEmpty');

     //store signup for second Step
	Route::post('/providerSignUpSecondStep', 'ProviderController@signUpSecondStep');
	Route::get('/providerSignUpSecondStep', 'ProviderController@echoEmpty');
 
    

	//Route::post('/activateProvider', 'ProviderController@activate_provider');
	//Route::get('/activateProvider', 'ProviderController@echoEmpty');

	Route::post('/providerLogin', 'ProviderController@providerLogin');
	Route::get('/providerLogin', 'ProviderController@echoEmpty');


	Route::post('/forgetPassword', 'ProviderController@forgetPassword');
	Route::get('/forgetPassword', 'ProviderController@echoEmpty');

	Route::post('/updatePassword', 'ProviderController@updatePassword');
	Route::get('/updatePassword', 'ProviderController@echoEmpty');

  

	Route::post('/prepareEditProfile', 'ProviderController@getProfileData');
	Route::get('/prepareEditProfile', 'ProviderController@echoEmpty');


	Route::post('/UpdateProfile', 'ProviderController@UpdateProfile');
	Route::get('/UpdateProfile', 'ProviderController@echoEmpty');


              // provider categories apis  

    Route::post('/getProviderMainCat', 'ProviderController@getProviderMainCats');
	Route::get('/getProviderMainCat', 'ProviderController@echoEmpty');

	 Route::post('/getProviderStoreCategories', 'ProviderController@getProviderStoreCategories');
	Route::get('/getProviderStoreCategories', 'ProviderController@echoEmpty');

 

    Route::post('/addProviderCategory', 'ProviderController@addProviderCategory');
	Route::get('/addProviderCategory', 'ProviderController@echoEmpty');

     
     Route::post('/editProviderCategory', 'ProviderController@editProviderCategory');
	Route::get('/editProviderCategory', 'ProviderController@echoEmpty');


	Route::post('/updateProviderCategory', 'ProviderController@updateProviderCategory');
	Route::get('/updateProviderCategory', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderCategory', 'ProviderController@deleteProviderCategory');
	Route::get('/deleteProviderCategory', 'ProviderController@echoEmpty');

   


        // provider Offers APIs//
 
   
     Route::post('/getProviderOffers', 'ProviderController@getProviderOffers');
	Route::get('/getProviderOffers', 'ProviderController@echoEmpty');


    Route::post('/addProviderOffer', 'ProviderController@addProviderOffer');
	Route::get('/addProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/addProviderOffer', 'ProviderController@addProviderOffer');
	Route::get('/addProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/editProviderOffer', 'ProviderController@editProviderOffer');
	Route::get('/editProviderOffer', 'ProviderController@echoEmpty');

	Route::post('/updateProviderOffer', 'ProviderController@updateProviderOffer');
	Route::get('/updateProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/payProviderOffer', 'ProviderController@payProviderOffer');
	Route::get('/payProviderOffer', 'ProviderController@echoEmpty');


	Route::post('/stopProviderOffer', 'ProviderController@stopProviderOffer');
	Route::get('/stopProviderOffer', 'ProviderController@echoEmpty');


    /*Route::post('/editProviderOffer', 'ProviderController@editProviderOffer');
	Route::get('/editProviderOffer', 'ProviderController@echoEmpty');*/

 



     // provider  products  APIs

	Route::post('/addProduct', 'ProviderController@addProduct');
	Route::get('/addProduct', 'ProviderController@echoEmpty');

	Route::post('/getProducts', 'ProviderController@getProducts');
	Route::get('/getProducts', 'ProviderController@echoEmpty');

	Route::post('/deleteProduct', 'ProviderController@delete_Product');
	Route::get('/deleteProduct', 'ProviderController@echoEmpty');


	Route::post('/prepareProductUpdate', 'ProviderController@prepare_Product_Update');
	Route::get('/prepareProductUpdate', 'ProviderController@echoEmpty');



    Route::post('/updateProduct', 'ProviderController@updateProduct');
	Route::get('/updateProduct', 'ProviderController@echoEmpty');

	
	//////// provider Jobs apis /////////////


	 Route::post('/getProviderJobs', 'ProviderController@providerJobs');
	Route::get('/getProviderJobs', 'ProviderController@echoEmpty');

 

    Route::post('/addProviderJob', 'ProviderController@addProviderJob');
	Route::get('/addProviderJob', 'ProviderController@echoEmpty');

     
     Route::post('/editProviderJob', 'ProviderController@editProviderJob');
	Route::get('/editProviderJob', 'ProviderController@echoEmpty');


	Route::post('/updateProviderJob', 'ProviderController@updateProviderJob');
	Route::get('/updateProviderJob', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderJob', 'ProviderController@deleteProviderJob');
	Route::get('/deleteProviderJob', 'ProviderController@echoEmpty');


	Route::post('/deleteProviderJob', 'ProviderController@deleteProviderJob');
	Route::get('/deleteProviderJob', 'ProviderController@echoEmpty');
     

    Route::post('/getProviderJobDetails', 'ProviderController@getJobDetails');
	Route::get('/getProviderJobDetails', 'ProviderController@echoEmpty');

	Route::post('/getJobApplicants', 'ProviderController@jobApplicants');
	Route::get('/getJobApplicants', 'ProviderController@echoEmpty');

 

	/////// ///////  Request for excellence  APIS //////////
 
     Route::post('/getProviderExcellenceRequests', 'ProviderController@getExcellenceRequests');
	Route::get('/getProviderExcellenceRequests', 'ProviderController@echoEmpty');



    Route::post('/addProviderExcellenceRequests', 'ProviderController@addExcellenceRequests');
	Route::get('/addProviderExcellenceRequests', 'ProviderController@echoEmpty');



	Route::post('/payProviderExcellenceRequests', 'ProviderController@payExcellenceRequests');
	Route::get('/payProviderExcellenceRequests', 'ProviderController@echoEmpty');

 
    Route::post('/ProviderExcellenceRequestDetails', 'ProviderController@ExcellenceRequestDetails');
	Route::get('/ProviderExcellenceRequestDetails', 'ProviderController@echoEmpty');

  
	Route::post('/editProviderPhone', 'ProviderController@update_provider_phone');
	Route::get('/editProviderPhone', 'ProviderController@echoEmpty');

	     ////////////////////// orders routes ///////////////////////

    Route::post('/ordersCounts', 'ProviderController@fetchOrdersCounts');
	Route::get('/ordersCounts', 'ProviderController@echoEmpty');

	Route::post('/getProviderOrders', 'ProviderController@getProviderOrders');
	Route::get('/getProviderOrders', 'ProviderController@echoEmpty');
 

	Route::post('/orderAcceptance', 'ProviderController@orderAcceptance');
	Route::get('/orderAcceptance', 'ProviderController@echoEmpty');
 

	Route::post('/changeOrderStatus', 'ProviderController@changeOrderStatus');
	Route::get('/changeOrderStatus', 'ProviderController@echoEmpty');
    

    Route::post('/getBalances', 'ProviderController@getProviderBalance');
	Route::get('/getBalances', 'ProviderController@echoEmpty');

	//------------------------------------------------------------------------------


    Route::post('/prepareProviderSearch', 'ProviderController@prepareSearch');
	Route::get('/prepareProviderSearch', 'ProviderController@echoEmpty');

	Route::post('/Providersearch', 'ProviderController@Providersearch');
	Route::get('/Providersearch', 'ProviderController@Providersearch');
	
 
 
	 

});