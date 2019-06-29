<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Validator;

class NotificationController extends Controller
{



    public function get_notifications(Request $request){
        
             $lang = $request->input('lang');

        if($lang == "ar"){
            $msg = array(
                0 => '',
                1 => 'رقم مقدم الخدمه مطلوب',
                2 => ' المستخدم  غير موجود',
                3 => 'لابد من ادخال النوع ',
                4 => 'النوع لابد ان يكون provider , user,delivery ',
                5 => 'المستهدم غير موجود ',
                6 => 'تم جلب البينات ',
                7 => 'فشل في جلب البينات ',
                8 => 'جميع الاشعارات تكون  0,1',
                9 => 'جميع الاشعارات مطلوبه ',
                10 => 'تم  تخديث البيانات بنجاح ',
                11 => 'تم ا حفظ البيانات بنجاح ',
                12 => 'لابد من تمرير حقل الحاله ',
                13 => 'قيمه الحالة يجب ان تكون list or count ',

                

            );
            $name ="ar";
        }else{
            $msg = array(
                0 => '',
                1 => 'Access_token is required',
                2 => 'Actor not exists',
                3 => 'Type field required',
                4 => 'Type must be only provider , user , delivery',
                5 => 'Provider not exists ',
                6 => 'Successfully retrieved data',
                7 => 'Failed to retrieve data',
                8 => 'All Notification must be in 0,1',
                9 => 'All fields required',
                10 => 'Data updated successfully',
                11 => 'Data Saved successfully',
                12 => 'Status field required',
                13 => 'status value  must be list or count'
            );

            $name="en";
        }

            $messages = array(
                'access_token.required' => 1,
                'type.required'         => 3,
                'type.in'               => 4,
                'in'                    => 8,
                'status.required'       => 12,
                'status.in'             => 13,
                'required'              => 9
            );

            $validator = Validator::make($request->all(), [
                'access_token'       => 'required',
                'type'               => 'required|in:user,provider,delivery',
                'status'             => 'required|in:list,count'
            ],$messages);
                

            if($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
            }


             $type = $request -> type;

            switch ($type) {
                case 'provider':
                     $actor = 'providers';
                     $table = 'providers';
                     $colum = 'provider_id';
                    break;

                    case 'user':
                     $actor = 'users';
                     $table = 'users';
                     $colum = 'user_id';
                    break;
                
                   case 'user':
                     $actor = 'deliveries';
                     $table = 'deliveries';
                     $colum = 'delivery_id';
                    break;

                default:
                         $actor = 'deliveries';
                         $table = 'deliveries';
                         $colum = 'delivery_id';

                    break;
            }
     
               $actor_id    = $this->get_id($request,$table,$colum);

                if($actor_id == 0 ){
                      return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
                }

              $check = DB::table($table)   -> where($colum,$actor_id) -> first();

              if(!$check){
                return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
              }

 
      $conditions=[];
       if($request  -> status == "count"){
        $conditions['seen'] =0;
       }

         if($conditions){

             $notifications = DB::table("notifications")
                            ->where("actor_id" ,$actor_id)
                            ->where("actor_type" , $type)
                            ->where($conditions)
                            ->select(
                                "notifications.id AS id",
                                "notifications." . $name . "_title AS title",
                                "notifications." . $name . "_content AS content",
                                DB::raw("DATE(notifications.created_at) AS create_date"),
                                DB::raw("TIME(notifications.created_at) AS create_time")
                            )
                            ->orderBy("notifications.id", "DESC")
                            ->get();
        }else{

             $notifications = DB::table("notifications")
                            ->where("actor_id" ,$actor_id)
                            ->where("actor_type" , $type)
                            ->select(
                                "notifications.id AS id",
                                "notifications." . $name . "_title AS title",
                                "notifications." . $name . "_content AS content",
                                "notifications.notification_type",
                                "notifications.action_id",
                                DB::raw("DATE(notifications.created_at) AS create_date"),
                                DB::raw("TIME(notifications.created_at) AS create_time")
                            )
                            ->orderBy("notifications.id", "DESC")
                            ->get();

                       
                    if(isset($notifications) && $notifications -> count() > 0){

                        foreach ($notifications as $key => $notification) {

                             switch ($notification -> notification_type) {
                                 case '1':
                                     $notification -> actionId  = $notification -> action_id;
                                     $notification -> actionType = 'Orders';
                                     break;

                                case '6':
                                     $notification -> actionId  = $notification -> action_id;
                                     $notification -> actionType = 'Offers';
                                     break;

                                case '4':
                                     $notification -> actionId  = 0;
                                     $notification -> actionType = 'Admin';
                                     break;    
                                         
                                 
                                 default:
                                      $notification -> actionId  = 0;
                                      $notification -> actionType = 'Admin';
                                     break;
                             }
                                                         
                               
                             unset($notification -> notification_type);
                             unset($notification -> action_id);
                        }


                    }
 
        }
       


         if($request -> status == "count")                   {

                 return response()->json([
                "status" => true,
                "errNum" => 0,
                "msg"    => trans("messages.success"),
                "notificationsCount" => $notifications  -> count(),
                 ]);  

          }
 

         //if merge two array with date must usort them
       /* usort($notifications, function($a,$b) {
            return $a->create_date < $b->create_date;
        });*/


        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($notifications);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);

        // set url path for generted links
        $paginatedItems->setPath(url()->current());


        // update seen notification
      

        DB::table("notifications")
                            ->where("actor_id" ,$actor_id)
                            ->where("actor_type" , $type)
                    ->update([

                        "notifications.seen" => "1",

                    ]);

        return response()->json([
                "status" => true,
                "errNum" => 0,
                "msg"    => trans("messages.success"),
                "notifications" => $paginatedItems,
        ]);
    }



 

    public function getNotificationSettings(Request $request){  
         
         $lang = $request->input('lang');


 
        if($lang == "ar"){
            $msg = array(
                0 => '',
                1 => 'رقم مقدم الخدمه مطلوب',
                2 => ' المستخدم  غير موجود',
                3 => 'لابد من ادخال النوع ',
                4 => 'النوع لابد ان يكون providers , users ,deliveries ',
                5 => ' المستخدم  غير موجود ',
                6 => 'تم جلب البينات ',
                7 => 'لا يوجد اعدادات محفوظه حتي الان '
            );
        }else{
            $msg = array(
                0 => '',
                1 => 'Access_token is required',
                2 => 'Actor not exists',
                3 => 'Type field required',
                4 => 'Type must be only providers , users , deliveries',
                5 => 'Provider not exists ',
                6 => 'Successfully retrieved data',
                7 => 'ther is no setting info. uptil now'
            );
        }

            $messages = array(
                'access_token.required' => 1,
                'type.required'         => 3,
                'type.in'               => 4,
            );

            $validator = Validator::make($request->all(), [
                'access_token' => 'required',
                'type'         => 'required|in:users,providers,deliveries'
            ], $messages);

            if($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
            }


            $type = $request -> type;

            switch ($type) {
                case 'providers':
                     $actor = 'providers';
                     $table = 'providers';
                     $colum = 'provider_id';
                    break;

                    case 'users':
                     $actor = 'users';
                     $table = 'users';
                     $colum = 'user_id';
                    break;
                
                   case 'deliveries':
                     $actor = 'deliveries';
                     $table = 'deliveries';
                     $colum = 'delivery_id';
                    break;

                default:
                         $actor = 'deliveries';
                         $table = 'deliveries';
                         $colum = 'delivery_id';

                    break;
            }
     
               $actor_id    = $this->get_id($request,$table,$colum);

                if($actor_id == 0 ){
                      return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
                }

              $check = DB::table($table)   -> where($colum,$actor_id) -> first();

              if(!$check){
                return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
              }


              if($type == 'providers'){
                    $notificationSettings = DB::table('notification_settings') 
                                           -> where('type',$actor)  
                                            -> where('actor_id',$actor_id) 
                                            -> select('actor_id','type','new_order',
                                                      'cancelled_order',
                                                      'offer_request',
                                                      'admin_notify',
                                                      'ticket_notify',
                                                      'order_delay') 
                                           -> first();
                 
                 }elseif ($type == 'deliveries') {

                     $notificationSettings = DB::table('notification_settings') 
                                           -> where('type',$actor)  
                                            -> where('actor_id',$actor_id) 
                                            -> select('actor_id','type','new_order',
                                                      'cancelled_order',
                                                      'admin_notify',
                                                      'ticket_notify',
                                                      'recieve_orders') 
                                           -> first();
                 }
 


                  if($notificationSettings){
                         unset($notificationSettings -> id); 
                       return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[6],'settings' =>  $notificationSettings]);   
                  }
            
             return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);                      
    }



    public function saveNotificationSettings(Request $request){

             $lang = $request->input('lang');

        if($lang == "ar"){
            $msg = array(
                0 => '',
                1 => 'رقم مقدم الخدمه مطلوب',
                2 => ' المستخدم  غير موجود',
                3 => 'لابد من ادخال النوع ',
                4 => 'النوع لابد ان يكون providers , users ,deliveries ',
                5 => 'المستهدم غير موجود ',
                6 => 'تم جلب البينات ',
                7 => 'فشل في جلب البينات ',
                8 => 'جميع الاشعارات تكون  0,1',
                9 => 'جميع الاشعارات مطلوبه ',
                10 => 'تم  تخديث البيانات بنجاح ',
                11 => 'تم حفظ البيانات بنجاح ',

            );
        }else{
            $msg = array(
                0 => '',
                1 => 'Access_token is required',
                2 => 'Actor not exists',
                3 => 'Type field required',
                4 => 'Type must be only providers , users , deliveries',
                5 => 'Provider not exists ',
                6 => 'Successfully retrieved data',
                7 => 'Failed to retrieve data',
                8 => 'All Notification must be in 0,1',
                9 => 'All fields required',
                10 => 'Data updated successfully',
                11 => 'Data Saved successfully'
            );
        }

            $messages = array(
                'access_token.required' => 1,
                'type.required'         => 3,
                'type.in'               => 4,
                'in'                    => 8,
                'required'              => 9,
            );

            $validator = Validator::make($request->all(), [
                'access_token'       => 'required',
                'type'               => 'required|in:users,providers,deliveries',
                'new_order'          => 'required|in:0,1',
                'cancelled_order'    => 'required|in:0,1',
                'offer_request'      => 'required|in:0,1',
                'admin_notify'       => 'required|in:0,1',
                'ticket_notify'      => 'required|in:0,1',
                'order_delay'        => 'required|in:0,1',
            ], $messages);

            if($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
            }



              $type = $request -> type;

            switch ($type) {
                case 'providers':
                     $actor = 'providers';
                     $table = 'providers';
                     $colum = 'provider_id';
                    break;

                    case 'users':
                     $actor = 'users';
                     $table = 'users';
                     $colum = 'user_id';
                    break;
                
                   case 'deliveries':
                     $actor = 'deliveries';
                       $table = 'deliveries';
                     $colum = 'delivery_id';
                    break;

                default:
                         $actor = 'deliveries';
                         $table = 'deliveries';
                         $colum = 'delivery_id';

                    break;
            }
     
               $actor_id    = $this->get_id($request,$table,$colum);

                if($actor_id == 0 ){
                      return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
                }

              $check = DB::table($table)   -> where($colum,$actor_id) -> first();

              if(!$check){
                return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
              }

                $settings = DB::table('notification_settings') -> where('type',$actor)  -> where('actor_id',$actor_id) ;
                $set = $settings -> first();
  
             if($actor == 'providers'){

                $inputs = $request -> only('new_order','cancelled_order','offer_request','admin_notify','ticket_notify','order_delay');

                $inputs['recieve_orders'] = 0;

             }elseif ($actor == 'deliveries') {
                 
                  $inputs = $request -> only('new_order','cancelled_order','admin_notify','ticket_notify','recieve_orders');

                  $inputs['offer_request'] = 0;
                  $inputs['order_delay'] = 0;
             }
                

                if($set){

                    $settings -> update($inputs);

                     return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[10]]);


                }else{

                    
                   
                return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);

                }

          
    }


}
