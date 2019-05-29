<?php

namespace App\Http\Controllers;

/**
 * Class Crons.
 * it is a class to manage all repeated tasks like
 * Reset Meal quantaty
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
 
    }
    
    
   
   

    public function OfferExpireCron($now){
      
        $this->ExpireProviderOffer($now);
    }
 
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



 }
