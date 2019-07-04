<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
 use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Storage;

class OffersController extends Controller
{
	
	public function __construct(){
		
	}


   public function show(){


	  	$offers = DB::table('providers') 
						    -> join('providers_offers','providers.provider_id','providers_offers.provider_id') 
 						    ->select(
						    	'providers_offers.id AS offer_id',
						    	'offer_title',
						    	'providers_offers.provider_id',						    	 
						    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),
						    	 DB::raw("(SELECT (city.city_ar_name) FROM city WHERE city.city_id = providers.city_id) AS city_name"),
						    	  'start_date',
						    	  'end_date',
						    	  'expire',
						    	  'providers_offers.publish',
						    	  'paid',
						    	  'paid_amount',
						    	  'providers_offers.status',
						    	  'providers.store_name'
						    	  
						    	)
						    -> get();


         return view('cpanel.offers.show',compact('offers'));

   }


   public function getOrders($type){

   	   //types  0 -> new 1 -> approved 2-> paid 3-> expired 4 -> published 
           
            if(in_array($type, [0,1,2])){
            	$conditions[] = ['providers_offers.status', $type];
            	$conditions[] = ['providers_offers.expire', 0];
            }elseif ($type == 3) {
            	$conditions[] = ['providers_offers.expire', 1];
             } elseif($type == 4){
             	$conditions[] = ['providers_offers.publish',1];
            } 
 
		        if(!empty($conditions)){

		        	   $offers = DB::table('providers_offers')
		        	                ->where($conditions) 
								    -> join('providers','providers.provider_id','providers_offers.provider_id') 		  						    
								    ->select(
								    	'providers_offers.id AS offer_id',
								    	'offer_title',
								    	'providers_offers.provider_id',						    	 
								    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),
								    	  DB::raw("(SELECT (city.city_ar_name) FROM city WHERE city.city_id = providers.city_id) AS city_name"),
								    	  'start_date',
								    	  'end_date',
								    	  'expire',
								    	  'providers_offers.publish',
								    	  'paid',
								    	  'paid_amount',
								    	  'providers_offers.status',
								    	  'providers.store_name'	    	  
								    	)
								    -> get();
		        }else{


		        	 $offers = DB::table('providers') 
						    -> join('providers_offers','providers.provider_id','providers_offers.provider_id') 
 						    ->select(
						    	'providers_offers.id AS offer_id',
						    	'offer_title',
						    	'providers_offers.provider_id',						    	 
						    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),
						    	 DB::raw("(SELECT (city.city_ar_name) FROM city WHERE city.city_id = providers.city_id) AS city_name"),
						    	  'start_date',
						    	  'end_date',
						    	  'expire',
						    	  'providers_offers.publish',
						    	  'paid',
						    	  'paid_amount',
						    	  'providers_offers.status',
						    	  'providers.store_name'
						    	  
						    	)
						    -> get();
                     } 

 

         	return view('cpanel.offers.show',compact('offers','type'));
   }

  
 
}
