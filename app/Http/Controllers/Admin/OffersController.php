<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController as Push;
use App\Http\Controllers\NotificationController as NotifyC;
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
use Carbon\Carbon;
use DateTime;

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

         $type = 0; // only new orders 
         return view('cpanel.offers.show',compact('offers','type'));

   }


   public function getOffers($type){

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

  
	 public function offerAcceptRefuse(Request $request){
	      
	       $offer_id = $request -> offer_id;
	      
	       $status   = $request ->  status;
	       
	       $offer = DB::table('providers_offers') -> whereId($offer_id) -> first() ;

	       if(!$offer){
	       	return response() -> json(['error' => 'العرض غير موجود او ربما تمت حذفه ']);
	       }

          $provider = DB::table('providers') ->  where('provider_id',$request -> provider_id) ->select('device_reg_id','provider_id') -> first();


 	       if(!$provider){

                return response() -> json(['error' => 'صاحب العرض غير موجود او ربما يكون  محذوف  ']);
	       }
	 
	 $notif_data = array();
	 if($status == 1) { // approved offer 

	     $updated = DB::table('providers_offers')  -> whereId($offer_id) -> update(['status' => '1' ,'expire' => 0]);
 
				$notif_data['title']      ='تم قبول العرض من قبل الاداره ';
			    $notif_data['message']    = "تم قبول العرض من قبل الاداره  عنوان العرض   {$offer -> offer_title}";
			    $notif_data['offer_id']   = $offer_id;
			    $notif_data['notif_type'] = 'offer';
			     
  
      }elseif ($status == 0) {  // refuse offer
       	 
       	  $updated = DB::table('providers_offers')  -> whereId($offer_id) -> update(['expire' => '1' ,'status' => '0']);
   
				$notif_data['title']      = 'تم رفض العرض من قبل الاداره ';
			    $notif_data['message']    = "تم رفض العرض من قبل الاداره بعنوان  {$offer -> offer_title}";
			    $notif_data['order_id']   = $offer_id;
			    $notif_data['notif_type'] = 'offer';
			     
       } else{
 
            	return response() -> json(['error' => 'حاله الطلب غير صحيحه من فضلك حاول مجداا ']);
       }
  
	     if($updated){

	     	        $providerAllowOffersNotify = (new NotifyC()) -> check_notification($request -> provider_id,'providers','offer_request'); 


 
	     	 //send notification to mobile Firebase to provider  
			    if($providerAllowOffersNotify == 1 && $provider -> device_reg_id && $provider -> device_reg_id != NULL){

			    	$push_notif = (new Push())->send($provider -> device_reg_id,$notif_data,(new Push())->provider_key);
			    }

               
                if($providerAllowOffersNotify == 1 ){
			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $notif_data['title']  ,
		                "ar_title"           => $notif_data['title']  ,
		                "en_content"         => $notif_data['message'],
		                "ar_content"         => $notif_data['message'],
		                "notification_type"  => 6,
		                "actor_id"           => $provider ->provider_id,
		                "actor_type"         => "provider",
		                "action_id"          => $offer_id
		            ]);
		         }   
 
		   return response() -> json(['success' => 'تم تغيير حاله العرض بنجاح وتم ابلاع صاحب العرض ','status'  => $status]);
 
	     }else{
	     	return response() -> json(['error' => 'فشل في  تغيير حاله العرض من فضلك حاول  مجددا ']);
	     }
 
	 }

public function offerPublishing(Request $request){
    
 
          $offer_id = $request -> offer_id;
	      
	       $status   = $request ->  status;
	       
	       $offer = DB::table('providers_offers') -> whereId($offer_id) -> first() ;

	       if(!$offer){
	       	return response() -> json(['error' => 'العرض غير موجود او ربما تمت حذفه ']);
	       }

          $provider = DB::table('providers') ->  where('provider_id',$request -> provider_id) ->select('device_reg_id','provider_id') -> first();


 	       if(!$provider){

                return response() -> json(['error' => 'صاحب العرض غير موجود او ربما يكون  محذوف  ']);
	       }
	 
	 $notif_data = array();
	 if($status == 1) { // approved offer 

	     $updated = DB::table('providers_offers')  -> whereId($offer_id) -> update(['publish' => '1']);
 
				$notif_data['title']      ='نشر العرض ';
			    $notif_data['message']    = "تم نشر العرض الخاص بكم  {$offer -> offer_title}";
			    $notif_data['offer_id']   = $offer_id;
			    $notif_data['notif_type'] = 'offer';
			     
  
      }elseif ($status == 0) {  // refuse offer
       	 
       	  $updated = DB::table('providers_offers')  -> whereId($offer_id) -> update(['publish' => '0']);
   
				$notif_data['title']      = 'تم ايقاف  نشر  العرض الخاص بكم ';
			    $notif_data['message']    = "تم ايقاف نشر العرض الخاص بكم  {$offer -> offer_title}";
			    $notif_data['order_id']   = $offer_id;
			    $notif_data['notif_type'] = 'offer';
			     
       } else{
 
            	return response() -> json(['error' => 'حاله الطلب غير صحيحه من فضلك حاول مجداا ']);
       }
  
	     if($updated){
 

            $providerAllowOffersNotify = (new NotifyC()) -> check_notification($request -> provider_id,'providers','offer_request'); 

          
	     	 //send notification to mobile Firebase to provider  
			    if($providerAllowOffersNotify == 1 && $provider -> device_reg_id && $provider -> device_reg_id != NULL  ){

			    	$push_notif = (new Push())->send($provider -> device_reg_id,$notif_data,(new Push())->provider_key);
			    }

			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $notif_data['title']  ,
		                "ar_title"           => $notif_data['title']  ,
		                "en_content"         => $notif_data['message'],
		                "ar_content"         => $notif_data['message'],
		                "notification_type"  => 6,
		                "actor_id"           => $provider ->provider_id,
		                "actor_type"         => "provider",
		                "action_id"          => $offer_id
		            ]);



 
		   return response() -> json(['success' => 'تم تغيير حاله الطلب بنجاح وتم اشعار التاجر بها ','status'  => $status]);
 
	     }else{
	     	return response() -> json(['error' => 'فشل في  تغيير حاله العرض من فضلك حاول  مجددا ']);
	     }

}




public function offerReports(){

      $request = request();  // get request params

        
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
		    	  'providers_offers.created_at',
		    	  'expire',
		    	  'providers_offers.publish',
		    	  'paid',
		    	  'paid_amount',
		    	  'providers_offers.status',
		    	  'providers.store_name'
		    	  
		    	)
		    -> get();

        if($request->has('from')){
             $from = Carbon::createFromFormat('Y-m-d',$request->from)->toDateString();
           $offers = $offers->where('start_date','>=',$from);
        }
        if ($request->has('to')) {
          $to = Carbon::createFromFormat('Y-m-d',$request->to)->toDateString();
          $offers = $offers->where('end_date','<=',$to);
        }
        if ($request->has('from') && $request->has('to')) {
          $from = Carbon::createFromFormat('Y-m-d',$request->from)->toDateString();
          $to = Carbon::createFromFormat('Y-m-d',$request->to)->toDateString();
          //$offers = $offers ->whereBetween('created_at', [$from, $to])->get();
          $offers = $offers ->where('start_date','=',$from) ->where('end_date','=',$to) ->get();
        }
        if ($request->has('status')) {
        	if($request-> status == 'pending')
                $offers = $offers->where('status','=',0);
            elseif ($request-> status == 'approved') {
            	$offers = $offers->where('status','=',1);
            }elseif ($request-> status == 'canceled') {
            	$offers = $offers->where('expire','=',1); 
            }elseif ($request -> status == 'unpublished') {
            	$offers = $offers->where('publish','=',0) -> where('status','2'); 
            }elseif ($request -> status == 'published') {
            	 $offers = $offers->where('publish','=',1); 
            }
        }


     
	 return view('cpanel.offers.reports',compact('offers','request'));
}


public function offersProfits(){

//types  0 -> new 1 -> approved 2-> paid 3-> expired 4 -> published 

 $offers = DB::table('providers_offers') 
		         -> join('providers','providers.provider_id','providers_offers.provider_id') 
		         -> where('providers_offers.status','2')
		         ->where('providers_offers.paid','1')
			    ->select(
		    	'providers_offers.id AS offer_id',
		    	'offer_title',
		    	'providers_offers.provider_id',						    	 
		    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),
		    	 DB::raw("(SELECT (city.city_ar_name) FROM city WHERE city.city_id = providers.city_id) AS city_name"),
 		    	  'start_date',
		    	  'end_date',
		    	  'providers_offers.created_at',
		    	  'expire',
		    	  'providers_offers.publish',
		    	  'paid',
		    	  'paid_amount',
		    	  'providers_offers.status',
		    	  'providers.store_name'
		    	  
		    	)
		    -> get();


             
             $total = 0;

		    if(isset($offers) && $offers -> count() > 0){

		    	foreach ($offers as $offer) {
		    		
		    		 $datetime1 = new DateTime($offer -> start_date);
					 $datetime2 = new DateTime($offer -> end_date);
					 $interval = $datetime1->diff($datetime2);
					 $days = $interval->format('%a');

                     
					 !empty($days) ? $offer -> days = $days: $offer -> days = 0;
		    	}


                $total = $offers ->sum('paid_amount'); 
		    }


	   return view('cpanel.offers.profits',compact('offers','request','total'));
		     

	}



}


