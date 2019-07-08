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

class ExcellentRequestsController extends Controller
{
	
	public function __construct(){
		
	}


   public function show(){
 
	  $providerRequests= DB::table('providers') 
						    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
  						    ->select(

						    	  'excellence_requests.id AS request_id',    	   	 
						    	  'excellence_requests.name as title',
						    	  'paid',
						    	  'paid_amount',
						    	   DB::raw("(SELECT (categories.cat_ar_name) FROM categories WHERE categories.cat_id = excellence_requests.main_category_id) AS category_name"),
						    	  'excellence_requests.start_date',
						    	  'excellence_requests.end_date',
						    	  'excellence_requests.status',
						    	   'excellence_requests.publish',
						    	  'providers.store_name',
						    	  'providers.provider_id'

	                              )
						    -> get();
 
			if(isset($providerRequests) && $providerRequests -> count() > 0){

				foreach ($providerRequests as $key => $providerRequest) {


					    if($providerRequest -> status == 0)

					    	$providerRequest -> order_date =  DATE('Y-m-d',strtotime($providerRequest ->start_date));
					    else 

					    	$providerRequest -> expire_date =  DATE('Y-m-d',strtotime($providerRequest ->end_date));
  
                            unset($providerRequest ->start_date);
                            unset($providerRequest ->end_date);
 
				}
 
			}


  
        $type = 0; // only new orders 
         return view('cpanel.excellent.show',compact('providerRequests','type'));


}


   public function getRequests($type){

   	   //types  0 -> new  1 -> approved 2-> paid  3-> expire
           
            if($type == 0){

            	
            	$inCondition = ['0','1'];  // new and approved
            	$conditions[] = ['excellence_requests.paid', '0'];      // not paid
            	$conditions[] = ['excellence_requests.publish', '0'];   // not publish
            	//$conditions[] = ['excellence_requests.status','!=', '3']; //expire 

             } 


             if($type == 1){

             	//paid request 
 
             	$conditions[] = ['excellence_requests.paid', '1'];      //  paid
             	//$conditions[] = ['excellence_requests.status','!=', '3']; //expire 
             	$inCondition = ['2'];  // new and approved
  
             }


 
		        if(!empty($conditions) && !empty($inCondition)){


				  $providerRequests= DB::table('providers') 
									    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
									    -> where($conditions)
									    ->whereIn('excellence_requests.status', $inCondition)
			  						    ->select(

									    	  'excellence_requests.id AS request_id',    	   	 
									    	  'excellence_requests.name as title',
									    	  'paid',
									    	  'paid_amount',
									    	  'excellence_requests.publish',
									    	   DB::raw("(SELECT (categories.cat_ar_name) FROM categories WHERE categories.cat_id = excellence_requests.main_category_id) AS category_name"),
									    	  'excellence_requests.start_date',
									    	  'excellence_requests.end_date',
									    	  'excellence_requests.status',
									    	  'providers.store_name',
									    	  'providers.provider_id'

				                              )
									    -> get();


 
		        }else{


		        	  $providerRequests= DB::table('providers') 
									    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
			  						    ->select(

									    	  'excellence_requests.id AS request_id',    	   	 
									    	  'excellence_requests.name as title',
									    	  'paid',
									    	  'paid_amount',
									    	   DB::raw("(SELECT (categories.cat_ar_name) FROM categories WHERE categories.cat_id = excellence_requests.main_category_id) AS category_name"),
									    	  'excellence_requests.start_date',
									    	  'excellence_requests.end_date',
									    	  'excellence_requests.status',
									    	  'excellence_requests.publish',
									    	  'providers.store_name',
									    	  'providers.provider_id'

				                              )
									    -> get();


                } 


	if(isset($providerRequests) && $providerRequests -> count() > 0){

					foreach ($providerRequests as $key => $providerRequest) {


						    if($providerRequest -> status == 0)

						    	$providerRequest -> order_date =  DATE('Y-m-d',strtotime($providerRequest ->start_date));
						    else 

						    	$providerRequest -> expire_date =  DATE('Y-m-d',strtotime($providerRequest ->end_date));
	  
	                            unset($providerRequest ->start_date);
	                            unset($providerRequest ->end_date);
	 
					}
	 
			}


   
 

         	return view('cpanel.excellent.show',compact('providerRequests','type'));
   }

  
	 public function excellentAcceptRefuse(Request $request){
	      
	       $request_id = $request -> request_id;
	      
	       $status     = $request ->  status;
	       
	       $request    = DB::table('excellence_requests') -> whereId($request_id) -> first() ;

	       if(!$request){

	       	return response() -> json(['error' => 'الطلب غير  موجود او ربما تمت حذفه ']);
	       }

          $provider = DB::table('providers') ->  where('provider_id',$request -> provider_id) ->select('device_reg_id','provider_id') -> first();


 	       if(!$provider){

                return response() -> json(['error' => 'صاحب العرض غير موجود او ربما يكون  محذوف  ']);
	       }
	 
	 $notif_data = array();
	 if($status == 1) { // approved request 

	     $updated = DB::table('excellence_requests')  -> whereId($request_id) -> update(['status' => '1']);
 
				$notif_data['title']      ='تم قبول الطلب  من قبل الاداره ';
			    $notif_data['message']    = "تم قبول  الطلب  من قبل الاداره  عنوان العرض   {$request -> name}";
			    $notif_data['request_id']   = $request_id;
			    $notif_data['notif_type'] = 'excellentrequest';
			     
  
      }elseif ($status == 0) {  // refuse offer
       	 
       	  $updated = DB::table('excellence_requests')  -> whereId($request_id) -> update(['status' => '0']);
   
				$notif_data['title']        = 'تم رفض  الطلب  من قبل الاداره ';
			    $notif_data['message']      = "تم رفض  الطلب  من قبل الاداره بعنوان  {$request  -> name}";
			    $notif_data['request_id']   = $request_id;
			    $notif_data['notif_type']   = 'excellentrequest';
			     
       } else{
 
            	return response() -> json(['error' => 'حاله الطلب غير صحيحه من فضلك حاول مجداا ']);
       }
  
	     if($updated){

    
	     	   $providerAllowOffersNotify = (new NotifyC()) -> check_notification($request -> provider_id,'providers','offer_request'); 
 
	     	 //send notification to mobile Firebase to provider  
			    if( $providerAllowOffersNotify == 1 && $provider -> device_reg_id && $provider -> device_reg_id != NULL){

			    	$push_notif = (new Push())->send($provider -> device_reg_id,$notif_data,(new Push())->provider_key);
			    }

			    if( $providerAllowOffersNotify  == 1){

				      DB::table("notifications")
			            ->insert([
			                "en_title"           => $notif_data['title']  ,
			                "ar_title"           => $notif_data['title']  ,
			                "en_content"         => $notif_data['message'],
			                "ar_content"         => $notif_data['message'],
			                "notification_type"  => 6,
			                "actor_id"           => $provider ->provider_id,
			                "actor_type"         => "provider",
			                "action_id"          => $request_id
			            ]);
		         }   
 
		   return response() -> json(['success' => 'تم تغيير حاله العرض بنجاح وتم ابلاع صاحب العرض ','status'  => $status]);
 
	     }else{
	     	return response() -> json(['error' => 'فشل في  تغيير حاله العرض من فضلك حاول  مجددا ']);
	     }
 
	 }

public function excellentPublishing(Request $request){
    
 
          $request_id = $request -> request_id;
	      
	       $status   = $request ->  status;
	       
	       $excellentRequest = DB::table('excellence_requests') -> whereId($request_id) -> first() ;

	       if(!$excellentRequest){
	       	return response() -> json(['error' => 'الطلب غير موجود او ربما تمت حذفه ']);
	       }

          $provider = DB::table('providers') ->  where('provider_id',$request -> provider_id) ->select('device_reg_id','provider_id') -> first();


 	       if(!$provider){

                return response() -> json(['error' => 'صاحب العرض غير موجود او ربما يكون  محذوف  ']);
	       }
	 
	 $notif_data = array();
	 if($status == 1) { // publish

	     $updated = DB::table('excellence_requests')  -> whereId($request_id) -> update(['publish' => '1']);
 
				$notif_data['title']      ='نشر العرض ';
			    $notif_data['message']    = "تم نشر العرض الخاص بكم  {$excellentRequest -> name}";
			    $notif_data['request_id']   = $request_id;
			    $notif_data['notif_type'] = 'excellentRequest';
			     
  
      }elseif ($status == 0) {  // unpublish
       	 
       	  $updated = DB::table('excellence_requests')  -> whereId($request_id) -> update(['publish' => '0']);
   
				$notif_data['title']      = 'تم ايقاف  نشر  الطلب  الخاص بكم ';
			    $notif_data['message']    = "تم ايقاف نشر  الطلب الخاص بكم  {$excellentRequest -> name}";
			    $notif_data['request_id']   = $request_id;
			    $notif_data['notif_type'] = 'excellentrequest';
			     
       } else{
 
            	return response() -> json(['error' => 'حاله الطلب غير صحيحه من فضلك حاول مجداا ']);
       }
  
	     if($updated){
 

                   $providerAllowOffersNotify = (new NotifyC()) -> check_notification($request -> provider_id,'providers','offer_request'); 

	     	 //send notification to mobile Firebase to provider  
			    if($providerAllowOffersNotify == 1 && $provider -> device_reg_id && $provider -> device_reg_id != NULL){

			    	$push_notif = (new Push())->send($provider -> device_reg_id,$notif_data,(new Push())->provider_key);
			    }


              if($providerAllowOffersNotify == 1 )
              {
			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $notif_data['title']  ,
		                "ar_title"           => $notif_data['title']  ,
		                "en_content"         => $notif_data['message'],
		                "ar_content"         => $notif_data['message'],
		                "notification_type"  => 6,
		                "actor_id"           => $provider ->provider_id,
		                "actor_type"         => "provider",
		                "action_id"          => $request_id
		            ]);
               }


		   return response() -> json(['success' => 'تم تغيير حاله الطلب بنجاح وتم اشعار التاجر بها ','status'  => $status]);
 
	     }else{
	     	return response() -> json(['error' => 'فشل في  تغيير حاله العرض من فضلك حاول  مجددا ']);
	     }

}

 

public function excellentReports(){


        $request = request();
      
 
       // get request params
 
	
    	  $providerRequests= DB::table('providers') 
						    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
  						    ->select(

						    	  'excellence_requests.id AS request_id',    	   	 
						    	  'excellence_requests.name as title',
						    	  'paid',
						    	  'paid_amount',
						    	   DB::raw("(SELECT (categories.cat_ar_name) FROM categories WHERE categories.cat_id = excellence_requests.main_category_id) AS category_name"),
						    	  'excellence_requests.start_date',
						    	  'excellence_requests.end_date',
						    	  'excellence_requests.status',
						    	  'excellence_requests.publish',
						    	  'providers.store_name',
						    	  'providers.provider_id'

	                              )
						    -> get();


		        if($request->has('from')){
		             $from = Carbon::createFromFormat('Y-m-d',$request->from)->toDateString();
		             $providerRequests = $providerRequests->where('start_date','>=',$from);
		        }
		        if ($request->has('to')) {
		          $to = Carbon::createFromFormat('Y-m-d',$request->to)->toDateString();
		          $providerRequests = $providerRequests->where('end_date','<=',$to);
		        }
		        if ($request->has('from') && $request->has('to')) {
		          $from = Carbon::createFromFormat('Y-m-d',$request->from)->toDateString();
		          $to = Carbon::createFromFormat('Y-m-d',$request->to)->toDateString();
		          //$offers = $offers ->whereBetween('created_at', [$from, $to])->get();
		          $providerRequests = $providerRequests ->where('start_date','=',$from) ->where('end_date','=',$to) ->get();
		        }

		        //0 new 1 approved 2 paid 3 expire

		        if ($request->has('status')) {
		        	if($request-> status == 'pending')
		                $providerRequests = $providerRequests->where('status','=',0);
		            elseif ($request-> status == 'approved') {
		            	$providerRequests = $providerRequests->where('status','=',1);
		            }elseif ($request-> status == 'canceled') {
		            	$providerRequests = $providerRequests->where('status','=',3); 
		            }elseif ($request -> status == 'unpublished') {
		            	$providerRequests = $providerRequests->where('publish','=',0) -> where('paid','1'); 
		            }elseif ($request -> status == 'published') {
		            	 $providerRequests = $providerRequests->where('publish','=',1)-> where('paid','1'); 
		            }
		        }

		 	 return view('cpanel.excellent.reports',compact('providerRequests','request'));
		}


		public function excellentProfits(){

 //types  0 -> new  1 -> approved 2-> paid  3-> expire
			  $providerRequests= DB::table('providers') 
									    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
									     -> where('excellence_requests.status','2')
		                                 -> where('excellence_requests.paid','1')
			  						     ->select(
									    	  'excellence_requests.id AS request_id',    	   	 
									    	  'excellence_requests.name as title',
									    	  'paid',
									    	  'paid_amount',
									    	   DB::raw("(SELECT (categories.cat_ar_name) FROM categories WHERE categories.cat_id = excellence_requests.main_category_id) AS category_name"),
									    	  'excellence_requests.start_date',
									    	  'excellence_requests.end_date',
									    	  'excellence_requests.status',
									    	  'excellence_requests.publish',
									    	  'providers.store_name',
									    	  'providers.provider_id'

				                              )
									    -> get();


            $total = 0;

				    if(isset($providerRequests) && $providerRequests -> count() > 0){

				    	foreach ($providerRequests as $providerRequest) {
				    		
				    		 $datetime1 = new DateTime($providerRequest -> start_date);
							 $datetime2 = new DateTime($providerRequest -> end_date);
							 $interval = $datetime1->diff($datetime2);
							 $days = $interval->format('%a');


							 !empty($days) ? $providerRequest -> days = $days: $providerRequest -> days = 0;
				    	}
				    	  $total = $providerRequests ->sum('paid_amount'); 
				    }
 

                return view('cpanel.excellent.profits',compact('providerRequests','request','total'));

		}

}


