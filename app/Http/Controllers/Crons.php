<?php

namespace App\Http\Controllers;

/**
 * Class Crons.
 * it is a class to manage all repeated tasks like
 * Refuse delayed orders
 * ..etc.
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
 use Log;
use App\Providers;
use App\Offer;
 use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Http\Controllers\PushNotificationController as Push;
use App\Http\Controllers\NotificationController as NotifyC;
 
class Crons extends Controller
{
    //method to prevent visiting any api link
    public function echoEmpty(){
        echo "";
    }



   public function __construct(Request $request){
       
       
          $api_email    = $request-> api_email;
            $api_password = $request-> api_password ;
            $get = DB::table('api_users')
                ->where('api_email', $api_email)
                ->where('api_pass', md5($api_password))
                ->first();
                
            if(!$get || $get == NULL){
                return response()->json('Unauthenticated.');
            }
            
      
   }
   
    public function cron_job(Request $request){
 
         
       
        date_default_timezone_set('Asia/Riyadh');
        $now =   date('Y-m-d'); 
        //$now = time();
 
        //second refuse all dismissed orders
        $this->OfferExpireCron($now);

        $this->requestsExpireCron($now);
 
    }
     

    public function OfferExpireCron($now){
      
        $this->ExpireProviderOffer($now);
    }
    // public function OfferExpireCron($now){
      
    //     $this->requestsExpireCron($now);
    // }

    
 
    protected function ExpireProviderOffer($now){
        
         
        $offers =  Offer::join('providers', 'providers_offers.provider_id', '=', 'providers.provider_id')
            ->where('expire',0) -> select( DB::raw('DATE(end_date) AS expire_date'),'providers.provider_id','providers.device_reg_id','offer_title','providers_offers.id','expire')->get();
            
            

        if(isset($offers) && $offers -> count() > 0){
            foreach($offers AS $offer){
                
                   if($offer -> expire_date  <  $now && $offer -> expire != 1){
                       
                       Offer::where('id', $offer -> id)
                        ->update(['expire' => 1]);
                                    
                  //send notification to the provider
                    $notif_data = array();
                    $notif_data['title']      = 'الاختار بحالة عرض';
                    $notif_data['message']    = 'لقد تم انتهاء مدة العرض الخاص بك بعنوان ' .$offer -> offer_title;
                    $notif_data['offer_id']   = $offer-> id;
                    $notif_data['notif_type'] = 'offers';
                    
                     DB::table("notifications")
                        ->insert([
                            "en_title" => "offer change status",
                            "ar_title" => $notif_data['title'],
                            "en_content" => "your offer with title {$offer -> offer_title} is expired",
                            "ar_content"  => $notif_data['message'],
                            "notification_type"  => 6,
                            "actor_id" => $offer -> provider_id,
                            "actor_type" => "provider",
                            "action_id" => $notif_data['offer_id']
            
                        ]);
                                
                    $push_provider_notif 	  = (new PushNotificationController()) ->send($offer -> device_reg_id, $notif_data, (new PushNotificationController())-> provider_key);
                            
                        
                   }
                    
            }
        }
    }

     protected function requestsExpireCron($now){
        
         
        $requests =  DB::table('excellence_requests') -> join('providers', 'excellence_requests.provider_id', '=', 'providers.provider_id')
            ->where('status','!=',3) -> select( DB::raw('DATE(end_date) AS expire_date'),'providers.provider_id','providers.device_reg_id','name','excellence_requests.id','excellence_requests.status')->get();
             

        if(isset($requests) && $requests -> count() > 0){
            foreach($requests AS $excellentrequest){
                
                   if($excellentrequest -> expire_date  <  $now && $excellentrequest -> status != 3){
                       
                       DB::table('excellence_requests') -> where('id', $requests -> id)
                        ->update(['status' => 3]);   // expire request after end date 
                                    
                  //send notification to the provider
                    $notif_data = array();
                    $notif_data['title']      = 'الاختار بحالة  الطلب ';
                    $notif_data['message']    = "قد تم انتهاء مدة  الطلب  الخاص بك بعنوان  {$excellentrequest -> name}" ;
                    $notif_data['request_id']   = $excellentrequest -> id;
                    $notif_data['notif_type'] = 'excellentrequest';
                    
                     DB::table("notifications")
                        ->insert([
                            "en_title" => $notif_data['title'],
                            "ar_title" => $notif_data['title'],
                            "en_content" => $notif_data['message'],
                            "ar_content"  => $notif_data['message'],
                            "notification_type"  => 6,
                            "actor_id" => $excellentrequest -> provider_id,
                            "actor_type" => "provider",
                            "action_id" => $request_id
            
                        ]);
                                
                    $push_provider_notif    = (new PushNotificationController()) ->send($excellentrequest -> device_reg_id, $notif_data, (new PushNotificationController())-> provider_key);
                            
                        
                   }
                    
            }
        }
    }


       // refuse delivery late orders 
    public function refuse_delay_orders_crone(){

         date_default_timezone_set('Asia/Riyadh');
         $now =   strtotime(date('Y-m-d')); 
 
          //get allowed time in min
        $settings = DB::table('app_settings')->first();
        if($settings != NULL){
            $time_in_min = $settings->time_in_min;
        }else{
            $time_in_min = 15;
        }
        //get all new orders for that day 
        $date   = date('Y-m-d', strtotime("-1 days"));

      $orders = DB::table('orders_headers')
                    ->where('status_id', 2)
                    ->where(DB::raw('DATE(orders_headers.created_at)'), ">=", $date)
                    ->join('rejectedorders_delivery','rejectedorders_delivery.order_id','orders_headers.order_id')
                    ->where('status',1)
                    ->select(
                              DB::raw('TIME(rejectedorders_delivery.created_at) AS created_time'),
                              'orders_headers.order_id',                              
                              'payment_type',
                              'total_value',
                              'orders_headers.provider_id',
                              'orders_headers.delivery_id',
                              'orders_headers.user_id' )
                    ->get();


        if(isset($orders) && $orders->count() > 0){
            foreach($orders AS $order){
                $created_at = strtotime($order->created_time);
 
                $diff = round(abs($now - $created_at) / 60,2);
                if($diff >= $time_in_min){

                    DB::table("orders_headers")
                        ->where('order_id', $order->order_id)->
                         update(['delivery_id' => 0]);

                     DB::table("rejectedorders_delivery")
                        ->where('order_id', $order->order_id)
                        ->where('delivery_id',$order->delivery_id)
                        ->update(['status' => 0]);

                $title   = "تجاوز الوقت المسموح للتوصيل -"  .$order -> order_id;
                $message = "تم جاوز الوقت المسموح به للموصل لتوصيل الطلب رقم  وتم الغاءه من قبل هذا الموصل واسناده للموصلين جميعا لقبول التوصبل ";

                $deliveryTitle   = "هناك طلب جديد  - ".$order -> order_id;
                $deliveryMessage =" تم اضافه طلب جديد يمكن الاطلاع عليه  - ".$order -> order_id;
                
                     //send to order's provider and delivery 
                $notif_data = array();
                $notif_data['title']      = $title;
                $notif_data['message']    = $message;
                $notif_data['order_id']   = $order->order_id;
                $notif_data['notif_type'] = 'order';

                //send to all deliveries as new order  
                $notif_data_delivery = array();
                $notif_data_delivery['title']      = $deliveryTitle;
                $notif_data_delivery['message']    = $deliveryMessage;
                $notif_data_delivery['order_id']   = $order->order_id;
                $notif_data_delivery['notif_type'] = 'order';



                    ////send notification for provider///
 
                     // check if provider allow recieve  delay order status notification 
              $providerAllowlatedStatus = (new NotifyC()) -> check_notification($order -> provider_id,'providers','order_delay'); 
                 
                    //send notification to mobile Firebase            
                if($providerAllowlatedStatus == 1){

                    $provider = DB::table('providers') -> where('provider_id',$order -> provider_id) -> select('device_reg_id') -> first();

                    if($provider){
                           
                           $push_notif = (new Push())->send($provider ->device_reg_id,$notif_data,(new Push())->provider_key);


                            if($providerAllowlatedStatus == 1){
                                  DB::table("notifications")
                                    ->insert([
                                        "en_title"           => $title,
                                        "ar_title"           => $title,
                                        "en_content"         => $message,
                                        "ar_content"         => $message,
                                        "notification_type"  => 1,
                                        "actor_id"           => $order -> provider_id,
                                        "actor_type"         => "provider",
                                        "action_id"           => $order -> order_id

                                    ]);
                             }    


                    }
                    
                }


                 $deliveyAllowlatedStatusdelay = (new NotifyC()) -> check_notification($order -> delivery_id,'deliveries','cancelled_order'); 
                 
                    //send notification to mobile Firebase            
                if($deliveyAllowlatedStatusdelay == 1){

                    $delivery = DB::table('deliveries') -> where('delivery_id',$order -> delivery_id) -> select('device_reg_id') -> first();

                    if($delivery){
                           
                         $push_notif = (new Push())->send($delivery ->device_reg_id,$notif_data,(new Push())->delivery_key);


                            if($deliveyAllowlatedStatusdelay == 1){
                                  DB::table("notifications")
                                    ->insert([
                                        "en_title"           => $title,
                                        "ar_title"           => $title,
                                        "en_content"         => $message,
                                        "ar_content"         => $message,
                                        "notification_type"  => 1,
                                        "actor_id"           => $order -> delivery_id,
                                        "actor_type"         => "delivery",
                                        "action_id"           => $order -> order_id

                                    ]);
                             }    


                    }
                    
                }
 

                    // send notification to all deliveries   

                      $this -> sendNotificationToDeliveries($notif_data_delivery,$order->order_id,$deliveryTitle,$deliveryMessage,$order -> delivery_id);
                }
            }
        }
 
      }


 protected function sendNotificationToDeliveries($notif_data,$id,$push_notif_title,$push_notif_message,$delivery_id){


      $deliveries = DB::table('deliveries') 
                    ->where('deliveries.delivery_id','!=',$delivery_id)
                    -> join('notification_settings','deliveries.delivery_id','=','notification_settings.actor_id') 
                    -> where('notification_settings.type','deliveries') 
                    -> where('notification_settings.new_order',1) 
                    -> get();


      if(isset($deliveries) && $deliveries -> count() > 0){

          foreach ($deliveries as $key => $delivery) {
 
               if($delivery -> device_reg_id){

                    $push_notif =(new Push())->send($delivery -> device_reg_id, $notif_data,(new Push())->delivery_key);

               }


           DB::table("notifications")
            ->insert([
                "en_title"           => $push_notif_title,
                "ar_title"           => $push_notif_title,
                "en_content"         => $push_notif_message,
                "ar_content"         => $push_notif_message,
                "notification_type"  => 1,
                "actor_id"           => $delivery -> delivery_id,
                "actor_type"         => "delivery",
                "action_id"          => $id

            ]);

             
          }
      }
  
        
             

   }




 }
