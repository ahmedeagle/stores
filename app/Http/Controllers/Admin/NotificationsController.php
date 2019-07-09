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
use App\Http\Requests\NotificationsRequest;
class NotificationsController extends Controller
{
	
	public function __construct(){
		
	}


   public function getNotifications(){
 
	    $table = "(SELECT provider_id AS id,full_name,token as access_token,'تاجر' AS type,device_reg_id FROM providers";
		$table .= " UNION ";

		$table .= "SELECT user_id AS id,full_name,token as access_token,'مستخدم ' AS type,device_reg_id FROM users";

        $table .= " UNION ";

		$table .= "SELECT delivery_id AS id,full_name,token as access_token,'موصل' AS type,device_reg_id FROM deliveries) AS tble";

		$actors = DB::table(DB::raw($table))->orderBy('id', 'DESC')->get();

 
     return view('cpanel.notifications.show',compact('actors'));
 
}

	public function sendNotifications(NotificationsRequest $request){

            $ids = $request -> ids;
            $notif_data = array();
            $notif_data['title']      = $request -> subject;
		    $notif_data['message']    = $request -> content;
 		    $notif_data['notif_type'] = 'admin_notifications';


 		    try{
               
                 foreach ($ids as $key => $id) {

             	      $provider = DB::table('providers') ->  where('token',$id) ->select('device_reg_id','provider_id') -> first();

             	       $user = DB::table('users') ->  where('token',$id) ->select('device_reg_id','user_id') -> first();

             	        $delivery = DB::table('deliveries') ->  where('token',$id) ->select('device_reg_id','delivery_id') -> first();

                        $actor_id   = 0;
                        $actor_type = 'provider';
                        $allowNotify= 0; 

			 	       if($provider){

                           $actor_id   = $provider -> provider_id ;
                           $actor_type = 'provider';

                          $providerAllowAdminNotify = (new NotifyC()) -> check_notification($actor_id,'providers','admin_notify');

                           $providerAllowAdminNotify == 1 ? $allowNotify = 1 :  $allowNotify = 0;

                         if(!empty($provider -> device_reg_id) && $allowNotify == 1) {
                           $push_notif = (new Push())->send($provider -> device_reg_id,$notif_data,(new Push())->provider_key);
                           }

				       } elseif ($user) {
				        
				           $actor_id   = $user -> user_id ;
                           $actor_type = 'user';

                            $userAllowAdminNotify = (new NotifyC()) -> check_notification($actor_id,'users','admin_notify');

                           $userAllowAdminNotify == 1 ? $allowNotify = 1 :  $allowNotify = 0;


                           if(!empty($user -> device_reg_id) && $allowNotify == 1){
                           $push_notif = (new Push())->send($user -> device_reg_id,$notif_data,(new Push())->user_key);
                           }
 
				       }elseif ($delivery) {

				       	   $actor_id   = $delivery -> delivery_id ;
                           $actor_type = 'delivery';

                            $deliveryAllowAdminNotify = (new NotifyC()) -> check_notification($actor_id,'deliveries','admin_notify');

                           $deliveryAllowAdminNotify == 1 ? $allowNotify = 1 :  $allowNotify = 0;


                           if(!empty($delivery -> device_reg_id) && $allowNotify == 1){
                           $push_notif = (new Push())->send($delivery -> device_reg_id,$notif_data,(new Push())->delivery_key);
                           }
				       	 
				       } 


				       if($allowNotify == 1){

					         DB::table("notifications")
				            ->insert([
				                "en_title"           => $request -> subject,
				                "ar_title"           => $request -> subject,
				                "en_content"         => $request -> content,
				                "ar_content"         => $request -> content,
				                "notification_type"  => 8,
				                "actor_id"           => $actor_id,
				                "actor_type"         => $actor_type,
				                "action_id"          => $actor_id,
				            ]);
			             }

             }

 		    }catch(\Throwable $e)
				 {					 
 
					return redirect() -> back()-> with('faild' , 'فشل في ارسال بعض   الاشعارات  ');
				 }

           
          
          return redirect() -> back()-> with('success' , 'تمت العمليه بنجاح ');
         
           

	}
 
 

}




