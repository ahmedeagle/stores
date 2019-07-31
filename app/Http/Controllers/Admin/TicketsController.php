<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController as Push;
use App\Http\Controllers\NotificationController as NotifyC;
use App\User;
use DB;
use Validator;
class TicketsController extends Controller {

    function __construct()
    {

    }


	public function index($type=null){

        // 1 -> fromuser 2-> fromprovider 3-> fromdelivery

        if(in_array($type,[1,2,3]))
        {

                if($type == 2){
                    $data['title']  = 'تذاكر  المتاجر ';
                    $actorType      = "provider";
                    $column         = "provider_id";
                    $table          = "providers";
                    $data['imag_path']      = env('APP_URL')."/public/providerProfileImages/";
                }elseif ($type == 1) {
                     $data['title'] = 'تذاكر العملاء';
                     $actorType     = "user";
                      $column       = "user_id";
                     $table         = "users";
                      $data['imag_path']     = env('APP_URL')."/public/userProfileImages/";
                }elseif($type == 3){

                     $data['title']  = 'تذاكر  الموصلين ';
                     $actorType      = "delivery";
                      $column        = "delivery_id";
                     $table          = "deliveries";
                     $data['imag_path']      = env('APP_URL')."/public/deliveryImages/";
                }
        }else{

            return redirect() -> back() -> with('errors','الرجاء المحاوله مجددا النوع غير موجود ');
        }

 
         
       $conditions[] = ['tickets.actor_type', $actorType];
 
        $data['type'] = $type;
        $data['tickets'] = DB::table("tickets")
                                ->join("ticket_types" , "ticket_types.id" , "tickets.type_id")
                                ->where($conditions)
                                ->select(
                                    "tickets.*",
                                    "ticket_types.ar_name AS type_name",
                                    DB::raw("(SELECT(full_name) FROM  {$table} WHERE {$table}.{$column} = tickets.actor_id) AS name "),

                                    DB::raw("(SELECT(profile_pic) FROM  {$table} WHERE {$table}.{$column} = tickets.actor_id) AS profile_pic ") 

                                )
                                ->get();

         return view('cpanel.tickets.index',$data);
    }
    public function get_reply($id){

        $data['ticket'] = DB::table("tickets")
                            ->join("ticket_types" , "ticket_types.id" , "tickets.type_id")
                            ->where("tickets.id" , $id)
                            ->select(
                                "tickets.*",
                                "ticket_types.ar_name AS type_name"
                            )
                            ->first();

 // 1 -> fromuser 2-> fromprovider 3-> fromdelivery

        if($data['ticket']->actor_type == "provider"){
            $data['type']  = 2;
            $data['title'] = 'تذاكر التجار';
            $name = DB::table("providers")->where("provider_id", $data['ticket']->actor_id)->first();
            $data['username'] = $name-> full_name;
        }elseif($data['ticket']->actor_type == "users"){
            $data['type']  = 1;
            $data['title'] = 'تذاكر العملاء';
            $name = DB::table("users")->where("user_id", $data['ticket']->actor_id)->first();
            $data['username'] = $name->full_name;
        }elseif($data['ticket']->actor_type == "delivery"){

            $data['type']  = 3;
            $data['title'] = 'تذاكر  الموصلين ';
            $name = DB::table("deliveries")->where("delivery_id", $data['ticket']->actor_id)->first();
            $data['username'] = $name->full_name;
 
        }
  
        DB::table("ticket_replies")
            ->where("ticket_id" , $id)
            ->where("FromUser" , $data['type'] )
            ->update([
                "seen" => "1"
            ]);

        $data['ticket_replys'] = DB::table("ticket_replies")
                                    ->where("ticket_id" , $id)
                                    ->get();

        return view('cpanel.tickets.replay',$data);

    }

    public function post_reply(Request $request)
    {
        $messages = [
            'content.required' => 'برجاء ادخال الرد'
        ];
        $rules = [
            'content' => 'required'
        ];

        $this->validate($request, $rules, $messages);

        DB::table("ticket_replies")
                    ->where("ticket_id" , $request->input("ticket_id"))
                    ->insert([
                        "reply"     => $request->input("content"),
                        "ticket_id" => $request->input("ticket_id"),
                        "FromUser"  => "0"  // from admin
                    ]);

            $actor = DB::table('tickets') -> where('id',$request->input("ticket_id")) -> select('actor_id','actor_type') -> first();


            if($actor){

                      $table = "users" ;
                      $colum = "user_id";
                      $key   = "user_key";

                  if($actor -> actor_type == 'user'){
                       $table = "users" ;
                       $colum = "user_id";
                       $key   = "user_key";

                  }elseif($actor -> actor_type == 'provider'){                      
                       $table = "providers" ;
                       $colum = "provider_id";
                       $key   = "provider_key";

                  }elseif($actor -> actor_type == 'delivery'){
                       $table = "deliveries" ;
                       $colum = "delivery_id";
                       $key   = "delivery_key";
                  }

 
                  $data = DB::table($table) -> where($colum,$actor -> actor_id) -> select('device_reg_id',$colum)->first();

                  if($data){
                      
                      if(!empty($data -> device_reg_id) &&  $data -> device_reg_id != NULL){
  
                            //check if actor allow tickets notifications 

                             $actorAllowticket_notify = (new NotifyC()) -> check_notification($actor -> actor_id,$table,'ticket_notify'); 

                             if($actorAllowticket_notify == 1 or  $actorAllowticket_notify == '1' ){

                                    $notif_data = array();
                                    $notif_data['title']      = 'تحديث علي تذكره ';
                                    $notif_data['message']    = "ت هناك تحديث من الاداره علي التذكره الخاصه بكم رقم  {$request->input("ticket_id")}";
                                    $notif_data['ticket_id']   = $request->input("ticket_id");
                                    $notif_data['notif_type']  = 'tickets';
 
                                $push_notif = (new Push())->send($data -> device_reg_id,$notif_data,(new Push())-> $key);

                                 DB::table("notifications")
                                        ->insert([
                                            "en_title"           => $notif_data['title']  ,
                                            "ar_title"           => $notif_data['title']  ,
                                            "en_content"         => $notif_data['message'],
                                            "ar_content"         => $notif_data['message'],
                                            "notification_type"  => 9,
                                            "actor_id"           => $actor -> actor_id,
                                            "actor_type"         => $actor -> actor_type,
                                            "action_id"          => $request->input("ticket_id")
                                        ]);


                             }

                      }

                  }
            }

       // $redValue = route('ticket.replay') . $request->input("ticket_id");
        return redirect()->back()->with("success" , "تمت العملية بنجاح");
    }


    public function closeTicket($id){

         $ticket = DB::table('tickets') -> where('id',$id) -> first();

         if(!$ticket){

           return abort('404');
         }

       $request = Request();

      $status =  $request -> action;

         if(in_array($status, ['open','close'])){ 
                 
                 $val = ($status == 'open') ? '0' : '1';

                 $updated = DB::table('tickets') -> where('id',$id) ->update([ 'solved' => $val  ]);
 
                if($updated){

                    return redirect()->back()->with("success" , "تمت العملية بنجاح "); 
                }else{

                    return redirect()->back()->with("errors" , "لم يتم تحديث اي بيانات "); 
                }
                

         }else{


           return redirect()->back()->with("errors" , "حاله التذكره لابد ان تكون open or close "); 
         }
    

    }
}
