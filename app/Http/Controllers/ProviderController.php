<?php

namespace App\Http\Controllers;

/**
 * Class ProviderController.
 * it is a class to manage all provider functionalities
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */

use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Memberships;
use App\Providers;
use App\Marketers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Storage;
use DateTime;
use Carbon\Carbon;
use App\Http\Controllers\PushNotificationController as Push;

class ProviderController extends Controller
{


	public function __construct(Request $request){
		 
			
			
			
		}
		
		  
   

	//method to prevent visiting any api link
	public function echoEmpty(){
		echo "";
	}

 


	protected function saveImage($data, $image_ext, $path){
	    
	    
	    
		if(!empty($data)){
		    
		     
    						  
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    					  
                        try{
                                
                                 
                         
                                                    
                        			// header('Content-Type: image/jpeg');
                        			// $data = base64_decode($data);
                        			// $name = $path.'img-'.str_random(4).'.jpg';
                        			// $target_file = base_path()."/public/".$name;
                        			// file_put_contents($target_file,$data);
                        			// return $name;
                        			// Log::debug("image_string: ". $data);
                        			$data = str_replace('\n', "", $data);
                        			$data = base64_decode($data);

                        			 try{

                                        $im   = imagecreatefromstring($data);

                        			 }catch(\Throwable $e)
                        			 {


			    							$errMsg = "Failed to upload image, please try again later";
			    						 
			    
			    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);

                        			 }

                        			 
                        			if ($im !== false) {
                        				$name = 'img-'.str_random(25).'.'.$image_ext;
                        				if ($image_ext == "png"){
                        					imagepng($im, $path . $name, 9);
                        				}else{
                        					imagejpeg($im, $path . $name, 100);
                        				}
                        
                        				return $name;
                        			} else {
                        				return "";
                        			}
                        		 
                            }catch(Exception $e){
                                
                                return response()->json(['status'=> false, 'errNum' => 30, 'msg' =>$errMsg]);
                            }
                            
                       
		
		
		}else{
			return "";
		}
		
		
		
	}
      

      //prepare providers signup page first step
	public function prepareSignUp(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$cat_col 	    = "cat_ar_name AS cat_name";
			$country_col    = "country_ar_name AS country_name";
			$membership_col ="membership_ar_name AS membership_name";
		}else{
			$cat_col 	    = "cat_en_name AS cat_name";
			$country_col    = "country_en_name AS country_name";
			$membership_col = "membership_en_name AS  membership_name";
		}

		
		//get memeberships
		$memberships = Memberships::where('publish', 1)->select('membership_id', $membership_col)->get();
        

		//get countries
		$countries = DB::table('country')->where('publish', 1)->select('country_id', $country_col, 'country_code')->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'countries' => $countries,'memberships' => $memberships]);
	}

          
	public function prepareSignUpSecondStep(Request $request){

 

		$lang = $request->input('lang');


          if($lang == "ar"){
			$msg = array(
				 
				1 =>  'لابد من توكن المستخدم ',
			 
			);
		}else{
			$msg = array(
				 
				1 => 'access_token required',
			 
			);
		}

		$messages = array(
 			'access_token.required' => 1
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
 		], $messages);



        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		if($lang == "ar"){
			$cat_col 	        = "cat_ar_name AS cat_name";
			$delivery_method_col = "method_ar_name AS delivery_method_name";
  		}else{
			$cat_col 	    = "cat_en_name AS cat_name";
			$delivery_method_col = "method_en_name AS delivery_method_name";
 		}


            //get provider by token "required -->access_token"

            
 		$provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))  -> select('provider_id AS id','full_name AS provider_name','category_id','status','publish','phoneactivated','store_name', 'phone', 'country_code','country_id', 'city_id','membership_id', 'token AS access_token','provider_rate','created_at','activate_phone_hash') ->first();

        //$code =  json_decode($provider  -> activate_phone_hash);
        
        
  		 
 		 
		  //get main categories
		$cats = Categories::where('publish', 1)->select('cat_id', $cat_col)->get();

 
                  if(isset($cats) && $cats -> count() > 0)  {

                           
                            foreach ($cats as $key => $cat) {

                            if ($provider -> category_id  ==  $cat -> cat_id ) {
                            	        

                            	  $cat    -> choosen = 1; 
 
                            }else{
  
                                    $cat    -> choosen = 0;        
                            }                     	   
                    }    

                  }


		 
        //get delivery Methods available 
	 	$delivery_methods = DB::table("delivery_methods")
	 	                        ->    select('method_id',$delivery_method_col)
                                ->    get();


                    
                  if(isset($delivery_methods) && $delivery_methods -> count() > 0)  {

                           
                            foreach ($delivery_methods as $key => $method) {

                            if ($provider -> delivery_method_id  ==  $method -> method_id ) {
                            	        

                            	  $method -> choosen = 1; 
                                   

                                  if($method_id ==2) 
                            	  $method -> price = $provider -> delivery_price;       

                            }else{
  
                                    $method -> choosen = 0;       

                            } 
                    	   
                    }    

                  }

 


		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '','data'=> $provider ,'delivery_methods' =>$delivery_methods,'cats' => $cats]);
	}



     //store providers first step sinup
	public function signUp(Request $request){
	     
 	    
		$lang   = $request->input('lang');
		$status = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
 				2 => 'الرقم السرى يجب الا يقل عن 8 حروف',
 				3 => 'كلمة المرور غير  متطابقه ',
				4 => 'رقم الجوال مستخدم من قبل',
 				5 => 'فشل التسجيل من فضلك حاول لاحقا',
 				6 => 'فئة المتجر غير موجوده ',
 				7 => 'الدولة غير موجوده ',
 				8 => 'المدينة غير موجوده ',
 				9 => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
 				

 			);
			
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'Password must not be less than 8 characters',
				3 => 'Password not confirmed',
				4 => 'Phone is already used',
 				5 => 'Failed to register, please try again later',
 				6 => 'membership type not exists',
 				7 => 'country   not exists',
 				8 => 'city  not exists',
 				9 => 'phone number format invlid it must start with 5 or 05 ',

 			);
		}

		$messages = array(
			'required'              => 1,
 			'min'                   => 2,
 			'password.confirmed'    => 3,
			'phone.unique'          => 4,			
			'membership_id.exists'  => 6,
			'country_id.exists'     => 7,
			'city_id.exists'        => 8,
			'phone.regex'           => 9,

 		);

		$validator = Validator::make($request->all(), [
			'full_name'              => 'required',
   			'store_name'             => 'required',
 			'phone'                  =>  array('required','unique:providers,phone','regex:/^(05|5)([0-9]{8})$/'),
			'country_code'           => 'required',
			'password_confirmation'  => 'required',
			'password'               => 'required|min:8|confirmed',			
			'country_id'             => 'required|exists:country,country_id',
			'membership_id'          => 'required|exists:memberships,membership_id',
			'city_id'                => 'required|exists:city,city_id',
 			'provider_token'         => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		    $rateCounter = 0;
			if($request->input('country_id') == 0 || $request->input('country_id') == "0" || is_null($request->input('country_id')) || empty($request->input('country_id'))){
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}

			if($request->input('city_id') == 0 || $request->input('city_id') == "0" || is_null($request->input('city_id')) || empty($request->input('city_id'))){
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
  


		   $data['image'] = "avatar_ic.png";

				    if($request->input('profile_pic')){
	 
	                    $image  = $request->input('profile_pic') ;
	                  //save new image   
	                    $image ->store('/','providers');
	                                       
	                  $nameOfImage = $image ->hashName();

	                $data['image'] =  $nameOfImage;

	 
		           }  


					$full_name = $request->input('full_name');

					$data['full_name']      = $full_name;
	 				$data['store_name']     = $request->input('store_name');
	 				$data['phone']          = $request->input('phone');
					$data['country_code']   =  $this -> checkCountryCodeFormate($request->input('country_code'));
					$data['password']       = $request->input('password');
					$data['country_id']     = $request->input('country_id');
					$data['city_id']        = $request->input('city_id');
					$data['membership_id']        = $request->input('membership_id');
	 				$data['status']         = $status;
	 				$data['provider_token'] = $request->input('provider_token');
	 				$data['provider_rate'] = 0;

				  

                          // send activation code to provider 


				    $code          = $this->generate_random_number(4);

			        $data['token'] = $this -> getRandomString(128);

			        $data['activate_phone_hash'] = json_encode([
			            'code'   => $code,
			            'expiry' => Carbon::now()->addDays(1)->timestamp,
			        ]);
			        
			        $message = (App()->getLocale() == "en")?
			                    "Your Activation Code is :- " . $code :
			                     "رقم الدخول الخاص بك هو :- " .$code ;
 
                    

				$id = "";


				try {
					DB::transaction(function () use ($data, &$id) {

						$id = Providers::insertGetId([
							'full_name'    => $data['full_name'],
 							'store_name'   => $data['store_name'],
 							'phone'        => $data['phone'],
							'country_code' => $data['country_code'],
							'password'     => md5($data['password']),
							'country_id'   => $data['country_id'],
							'membership_id'=> $data['membership_id'],
							'city_id'      => $data['city_id'],
							'status'       => $data['status'],
 							'device_reg_id'          => $data['provider_token'],
 							'provider_rate'          => $data['provider_rate'], 
 							'activate_phone_hash'    => $data['activate_phone_hash'],
 							'token'                   => $data['token'],
 							'profile_pic'             => $data['image']
						]);


						if($id){

							$inserts = array();
							 
							 //intialize balance with zero 
 							DB::table('balances')->insert(['actor_id' => $id, 'type' => 'provider','current_balance' => 0, 'due_balance' => 0]);

						}else{
							return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
						}


					});
  



                            //  return auth data to response 

					$providerData = Providers::where('provider_id', $id)->select('provider_id AS id','full_name AS provider_name', 'store_name', 'phone', 'country_code','country_id', 'city_id','membership_id', 'token AS access_token','status','provider_rate','created_at')->first();


                    $res = (new SmsController())->send($message , $providerData ->phone);
  

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $providerData ,'activation_code' => $code]);

				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
				}
 

			}
 
  

   //store providers second step sinup
	public function signUpSecondStep(Request $request){
	     
 	    
		$lang   = $request->input('lang');
		$status = 0;
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
 				2 => 'القسم التابع له المتجر غير موجود ',
 				3 => 'امتداد صوره غير صالح ',
				4 => 'نوع التوصيل غير موجود ',
 				5 => 'لابد من توكن المستخدم ',
 				6 =>  'لابد ان تكون  ارقام ',
 				7 => 'سعر التوصيل مطلوب',
 				8=> 'فشل التسجيل من فضلك حاول لاحقا',
 				9  => 'لابد ان تكون وسيله التوصيل علي شكل مصفوفه ',
 				10 => 'يجب علي الاقل اختيار وسبله توصيل واحده',
 				11 => 'امتداد الصوره غير موجود',
 				
  			);
  			
  			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'Category id doesn\'t exists',
				3 => 'image not valid',
				4 => 'delivery method not exists',
 				5=> 'access_token required',
 				6 => 'must be number ' ,
 				7 => 'delivery price  required',
 				8 => 'Failed to register, please try again later',
 				9 => 'delivery method must be an array',
 				10=> 'must choose at least one delivery method',
 				11 => 'image extension not exist'
 				
  			);
  			
  				$delivery_col = "delivery_methods.method_en_name AS delivery_method";
		}

		$messages = array(
           
			'category_id.required'        => 1,
			'commercial_photo.required'   => 1,
			'delivery_method_id.required' => 1,
			'longitude.required'          => 1,
			'latitude.required'           => 1,
 			'category_id.exists'          => 2,
 			'mimes'                       => 3,
 			'delivery_method_id.exists'   => 4,
			'access_token.required'       => 5,			
			'numeric'                     => 6,
			'delivery_price.required'     => 7,
			'delivery_method_id.array'   => 9,
			'delivery_method_id.min'     => 10,
		    'image_ext.required_with'   => 11,
			
  		);

 		$rules= [

			'category_id'            => 'required|numeric|exists:categories,cat_id',
   			'commercial_photo'       => 'required',
   			'image_ext'              => 'required_with:commercial_photo',
 			'delivery_method_id'     => 'required|array|min:1|exists:delivery_methods,method_id',
            'longitude'              => 'required',
			'latitude'               => 'required',
			'access_token'           => 'required',

		];
 
               //if delivery method is 2 this required price field in the request 


     if($request ->has('delivery_method_id')){


     	  if(! is_array($request -> delivery_method_id)){

     	  	return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);

     	  }

     	}  
 

     if(empty($request -> delivery_method_id) or !$request -> delivery_method_id){

       return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);

     }
 

        foreach ($request -> delivery_method_id as $method) {
        	  
        	  if(! is_numeric($method)){

        	  	return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
        	  }
        }
 
      $data=[];

		if(in_array(2,$request -> delivery_method_id)){
               
                  $rules['delivery_price'] = 'required';
                  $data['delivery_price']     = $request-> delivery_price;                           

		}
		 
		$validator = Validator::make($request->all(),$rules, $messages);
 

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

                  
					$data['category_id']            = $request-> category_id;
	 				 
	 				$data['longitude']              = $request-> longitude;
					$data['latitude']               = $request-> latitude;
 
                   

                   if($request -> known_phone ){
  
	                      $data['known_phone'] = $request -> known_phone;
                        
					}


					if($request -> has('commercial_photo')){
 
 	                                
     					 $image = $this->saveImage( $request -> commercial_photo, $request->input('image_ext'), 'providerProfileImages/');
     					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 12, 'msg' => $errMsg]);
    					}else{
    					    
            					 
        
        	                      $data['commercial_photo'] = $image;
    					}
			 
                        
					}else{
                                  $data['commercial_photo'] = "avatar_ic.png";
					}
 
					

					if($request ->  invitationCode){

						$data['invitationCode'] = $request ->  invitationCode;

                          // owner of invitation code to add new balance  

						$referalProvider = DB::table('providers') -> where('invitationCode', $data['invitationCode'] )  -> first();


						    if($referalProvider){

						    	$settings = DB::table('app_settings') -> first();

						    	if($settings){                                         
                                    $inviter_points =$settings -> inviter_points; 
                                    $invited_points =$settings -> invited_points; 
						    	}else{
                                      $inviter_points =0;
                                      $invited_points =0;
						    	}
 
						    DB::table('balances') -> where('actor_id',$referalProvider -> user_id) -> where('type','provider') -> update(['current_balance', DB::raw('current_balance + '. $inviter_points)]);

						    }
 
					}
 
                $id = $this->get_id($request,'providers','provider_id');  
                $provider = Providers::where('provider_id',$id);
                    
 
              DB::table('providers_delivery_methods') ->  where('provider_id',$id) -> delete();
		            
              	
		if($request -> delivery_method_id){
		     
		    foreach($request -> delivery_method_id as $deliveryId){
		        
		        DB::table('providers_delivery_methods') -> insert([
		               
		               'provider_id'       =>   $id,
		               'delivery_method'   =>   $deliveryId
		            
		            ]);
		        
		    }
		    
		    
		   
		}

		
             $data['status'] = 1;
             
				try {
					    DB::transaction(function () use ($data,$provider,&$id) {
  

						 $provider -> update($data);
 

					    });
  
 

                            //  return auth data to response 

					$providerData = Providers::where('provider_id', $id)
					    ->select(
					    	'provider_id AS id',
					    	'full_name AS provider_name',
					    	 'store_name', 
					    	 'phone', 
					    	 'country_code',
					    	 'country_id',
				    	     'city_id',
				    	     'membership_id',
				    	     'category_id',
				    	     'provider_rate',
				    	     'phoneactivated',
				    	     'status',
				    	     'publish',
				    	     'known_phone',
				    	     'token AS access_token',
				    	     'provider_rate',
				    	     'longitude',
				    	     'latitude',
				    	     'delivery_price',
				    	     DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/', providers.commercial_photo) AS commercial_photo"),
				    	     'known_phone',
				    	     'created_at'
					    	)->first();
					    	
					    	
					    	
					    		//get deliveries
		                	$deliveries = DB::table("delivery_methods")->select('method_id AS delivery_id',$delivery_col,
																DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$id.') > 0, 1, 0) AS choosen')

															)
													   ->get();
													   
													  

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $providerData,'deliveries' => $deliveries]);

				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
				}
  
			}
			
			
	protected function checkCountryCodeFormate($str){
	     
	    	   if(mb_substr(trim($str), 0, 1) === '+'){                         
                          return  $str;                     
                  }
                    
                  return '+'.$str;                  
	}
   

    public function activateAccount(Request $request){

          $lang = $request->input('lang');

          if($lang == "ar"){
			$msg = array(
				0 => 'تم التفعيل',
				1 => 'كود غير صحيح ',
				2 => 'لابد من  ادخال الكود ',
				3 =>  'لابد من توكن المستخدم ',
				4 =>  'فشل التفعيل من فضلك حاول لاحقا',
				5=> 'كود تفعيل غير صحيح ',
			);
		}else{
			$msg = array(
				0 => 'Activated successfully',
				1 => 'incorrect code',
			    2 => 'code is required',
				3 => 'access_token required',
				4 => 'Failed to activate, please try again later',
				5 => 'Code is not Correct',
			);
		}

		$messages = array(
			'code.required'         => 2,
			'access_token.required' => 3
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
			'code'         => 'required'
		], $messages);



        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


		$provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'));

		 $activate_phone_hash = $provider -> first() ->  activate_phone_hash;

		  $code                 = json_decode($activate_phone_hash) -> code;
 
		  if($code  != $request -> code)
		  {
              
             return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		  }
 
        $data['phoneactivated'] = "1";
        $data['status'] = "0";
        $data['activate_phone_hash'] = null;

        $provider -> update($data);
 
        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
    }


    public function resendActivationCode(Request $request){

      // required access_token 


    	 $lang = $request->input('lang');

          if($lang == "ar"){
			$msg = array(
				0 => 'تم  ارسال الكود بنجاح ',
 				1 =>  'لابد من توكن المستخدم ',
 				2 => 'فشل من فضلك حاول مجددا ',
			);
		}else{
			$msg = array(
				0 => 'code sent successfully',
 				1 => 'access_token required',
 				2 => 'failed try again later',
			);
		}

  

		$messages = array(
 			'access_token.required' => 1
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
 		], $messages);



        if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

 

     $data=[];

    $provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))  ;
 
    $code          = $this->generate_random_number(4);

  

    $data['activate_phone_hash'] = json_encode([
        'code'   => $code,
        'expiry' => Carbon::now()->addDays(1)->timestamp,
    ]);
 

     $provider -> update($data);

    $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                 "رقم الدخول الخاص بك هو :- " .$code ;
  
    $res = (new SmsController())->send($message , $provider -> first() ->phone);
   

    return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0] ,'activation_code' => $code]);

}


	public function providerLogin(Request $request){

		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله',
				3 => 'كلمة السر مطلوبه',
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل حسابكم من قبل الاداره ',
				6 => 'رقم الجهاز مطلوب',
				7=> 'لابد من تكمله التسجيل اولا ',
			);
			$city_col = "city.city_ar_name AS city_name";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
		}else{
			$msg = array(
				0 => 'Logined successfully',
				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'Password is required',
				4 => 'Wrong data',
				5 => 'admin not active your account',
				6 => 'provider_token is required',
				7 => 'need to  complete registeration '
			);
			$city_col = "city.city_en_name AS city_name";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
		}
		$messages = array(
				'phone.required'    => 1,
				'password.required' => 3,
				'provider_token.required' => 6
			);
		$validator = Validator::make($request->all(), [
			'phone'    => 'required',
			'password' => 'required',
			'provider_token' => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$getProvider = Providers::where('providers.password', md5($request->input('password')))
							->where(function($q) use ($request){
						        $q->where('providers.phone', $request->input('phone'))
						        /*  ->orWhere(DB::raw('CONCAT(providers.country_code,providers.phone)'), $request->input('phone'))*/;
						    })
							->join('city', 'providers.city_id', 'city.city_id')
							->select('providers.*', $city_col)
							->first();
			if($getProvider != NULL){

                      


                       //update device FCM token 
				Providers::where('provider_id', $getProvider->provider_id)->update(['device_reg_id' => $request->input('provider_token')]);

   
 				    	      
					    	
				$providerData = array(
					'id'              => $getProvider->provider_id,
					'provider_name'   => $getProvider->full_name,
					'store_name'      => $getProvider->store_name,
 					'phone' 		  => $getProvider->phone,
					'country_code'    => $getProvider->country_code,
 					'longitude'       => $getProvider->longitude,
					'latitude' 		  => $getProvider->latitude,
                    'commercial_photo'               =>env('APP_URL').'/public/providerProfileImages/'.$getProvider->commercial_photo,
					'membership_id' 		      => $getProvider->membership_id,
					'delivery_method_id' 		  => $getProvider->delivery_method_id,
					'category_id' 		          => $getProvider->category_id,
					'phoneactivated'              => $getProvider->phoneactivated,
					'known_phone'                 => $getProvider->known_phone,
					'access_token'                => $getProvider->token,
					'delivery_price'              => $getProvider->delivery_price,
					'phoneactivated'              => $getProvider->	phoneactivated,
                    'status'                      => $getProvider->status, 
				    'publish'                     => $getProvider->publish, 
					'country_id'      => $getProvider->country_id,
					'city_id'         => $getProvider->city_id,
 					'provider_rate'   => $getProvider->provider_rate,
 					'created_at'      => date('Y-m-d h:i:s', strtotime($getProvider->created_at))

				);
				
					//get deliveries
					
					$id = $getProvider->provider_id;
		                	$deliveries = DB::table("delivery_methods")->select('method_id AS delivery_id',$delivery_col,
																DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$id.') > 0, 1, 0) AS choosen'))
													   ->get();
				$code = json_encode($getProvider -> activate_phone_hash );									   

               
               //need to activate account by verfiy phone number
				if($getProvider-> status == 0 || $getProvider->status == 0){
					return response()->json(['status'=> false, 'errNum' => 7, 'data' => $providerData,'deliveries' => $deliveries, 'msg' => $msg[7]]);
				}


			 //admin not active the provider account 
				if($getProvider->publish == 0 || $getProvider->publish == 0){
					return response()->json(['status'=> false, 'errNum' => 5, 'data' => $providerData,'deliveries' => $deliveries,'msg' => $msg[5]]);
				}

			
                  
				return response()->json(['status'=> true, 'errNum' => 0, 'data' => $providerData,'deliveries' => $deliveries, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	


	public function forgetPassword(Request $request){
		  

		    $lang = $request->input('lang');
 

		if($lang == "ar"){
			$msg = array(
 				1 => 'رقم الهاتف مطلوب ',
				2 => 'رقم هاتف غير صحيح ',
				3 => 'رقم الهاتف غير موجود ',
				4 => 'تم ارسال كود تفعيل الي هاتفك ',
				5 => 'رقم الهاتف غير مفعل ',
				
			);
			 
		}else{
			$msg = array(
 				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'phone doesn\'t exists',
				4 => 'activation code sent successfully',
  				5 => 'phone not active'    
			);
			 
		}
	        $rules    = [
                   "phone" => "required|numeric|exists:providers,phone"
		        ];

		        $messages = [
		                "required" => 1,
		                "numeric"  => 2,
		                "exists"   => 3
		        ];
		        
		        $validator  = Validator::make($request->all(), $rules, $messages);

		        if($validator->fails()){
		            $error = $validator->errors()->first();
		            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		        }

		        //select provider base on his/her phone number if exists
		        $providerData = DB::table("providers")->where("phone" , $request->input("phone"))->select("provider_id")->first();

		        $provider = Providers::where('provider_id',$providerData -> provider_id);
                 

 		        if($provider -> first()->  phoneactivated == '0' ){

		            return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		        }


		        $code = $this -> generate_random_number(4);

		        $message = (App()->getLocale() == "en")?
		            "Your Activation Code is :- " . $code :
		            $code . "رقم الدخول الخاص بك هو :- " ;

		        $activate_phone_hash = json_encode([
		            'code'   => $code,
		            'expiry' => Carbon::now()->addDays(1)->timestamp,
		        ]);

		        $provider -> update([
		        	 'activate_phone_hash'   => $activate_phone_hash,
		        ]);

		        (new SmsController())->send($message ,$provider ->first()->phone);

		        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4] , "access_token" => $provider -> first() ->token,'activation_code' => $code]);

	}

	 

	   public function updatePassword(Request $request){

           $lang = $request->input('lang');

        $rules      = [
            "password"      => "required|min:8|confirmed",
            "access_token"  => "required"
        ];

        $messages   = [
            "required"              => 1,
            'password.min'          => 2,
            'password.confirmed'    => 3,
        ];



		if($lang == "ar"){
			$msg = array(
 				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور  8 احرف ع الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
 				4 => 'تم تغيير كلمة  المرور بنجاح ',
				
			);
			 
		}else{
			$msg = array(
 				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
   				4 => 'password successfully updated'    
			);
			 
		}

       
        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

        $provider = Providers::where('provider_id',$this->get_id($request,'providers','provider_id'))
                        -> update([
                                      
                                         'password'              =>  md5($request->input('password')),
                                         'activate_phone_hash'   => null
                                 ]);

               
 

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
    }

	public function getProfileData(Request $request){

		$lang = $request->input('lang');

		if($lang == "ar"){
 			$country_col              = "country_ar_name AS country_name";
			$city_col                 = "city_ar_name AS city_name";
			$cat_col                  = "categories.cat_ar_name AS cat_name";
 			$msg = array(
				0 => '',
				1 => 'توكن المستخدم مطلوب',
 				2 => 'لا يوجد بيانات'
			);
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method_name";
		}else{
 			$country_col = "country_en_name AS country_name";
			$city_col    = "city_en_name AS city_name";
			$cat_col     ="categories.cat_en_name AS cat_name";
 			$msg = array(
				0 => '',
				1 => 'access_token is required',
 				2 => 'There is no data'
			);
			 $delivery_col = "delivery_methods.method_en_name AS delivery_method_name";
		}

		$messages = array(
			'required' => 1,
 		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required'
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{



            if($this->get_id($request,'providers','provider_id') == 0){
                 
                 
                 return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
                
            }
         
            
		        
           $providerData = DB::table("providers") 
                             ->where('provider_id',$this->get_id($request,'providers','provider_id'))
                             ->join("city" , "providers.city_id" , "city.city_id")
                             ->select(
										"providers.provider_id",
										"providers.full_name AS provider_name",
										'providers.phone',
										'providers.category_id',
										'providers.store_name',
										'providers.longitude',
										'providers.latitude',
										'providers.country_code',
										'providers.country_id',
										'providers.token AS access_token',
										'providers.city_id',
										'providers.delivery_price',
										'providers.delivery_method_id',
										'city.' .$city_col,
		
										DB::raw("CONCAT('". url('/') ."','/providers/',providers.profile_pic) AS profile_pic")
									)	
                             ->first();
                             
                             
                              
 
			$providerCountry  = $providerData->country_id;
			$providerCity     = $providerData->city_id;
			$providerCategory = $providerData->category_id;


                // get all countries and add choosen to provider registed country

			$countries       = 
			          DB::table('country')
			                ->where('publish', 1)
			                ->select(
			                	      'country_id',
			                	       $country_col, 
			                	       DB::raw('IF(country_id = '.$providerCountry.', true, false) AS choosen'), 
			                	       'country_code'
			                	   )
			                ->get();


             //get main categories
             
             if($providerCategory){
          		$cats = Categories::where('publish', 1)
		              ->select(
			         'cat_id',
			          $cat_col,
			           DB::raw('IF(cat_id = '.$providerCategory.', true, false) AS choosen')

			     )->get();
			     
		} else{
		    
		    $cats=[];
		}   

  


			$cities          = 

			         DB::table('city')
			                  ->select(
			                  	'city_id', 
			                  	$city_col,
			                  	 DB::raw('IF(city_id = '.$providerCity.', 1, 0) AS choosen')
			                  	)->get();
 
	        //get delivery Methods available 
	 	  
	 	                    $id = $providerData ->provider_id;
		                	$delivery_methods = DB::table("delivery_methods")->select('method_id AS method_id',$delivery_col,
									DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$id.') > 0, 1, 0) AS choosen'))
													   ->get();
													   
 
			return response()->json([
										'status'       => true,
										'errNum'       => 0,
										'msg' 		   => $msg[0],
										'data'         => $providerData,
										'countries'    => $countries,
										'cities'       => $cities,
										'cats'         => $cats,
										'delivery_methods' => $delivery_methods

 									]);
		}
	}

	public function UpdateProfile(Request $request){

 
		$lang = $request->input('lang');

		if($lang == "ar"){
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'الدولة غير موجودة ',
				3 => 'المدينة  غير موجودة',
				4 => '',
				5 => 'فشلت العمليه من فضلاك حاول لاحقا',
				6 => 'نوع التوصيل غير موجود ',
				7 => 'التصنيف غير موجود ',
				8 => ' التوصيل لابد ان يكون رقم صحيح ',
				9 => 'صوره الملف الشخصي غير صالحة ',
				10 => 'رقم الهاتف  لابد ان يكون ارقام ',
				11 => 'صيغة رقم الهاتف خطا لابد ان تبدا ب 5 او 05 ',
				12 => 'لابد من ادخال سعر التوصيل',
				13 => 'طرق التوصيل يجب ان تكون علي شكل مصفوفه ',
 				14=> 'لابد من اهتيار طريقه توصيل واحده ع الاقل ',
 				15 => 'التاجر غير موجود ',
 				16 => 'رقم الهاتف موجود من قبل من فضلك ادخل رقم اخر '
			);
			
			
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			
		}else{
			$msg = array(
				0 => 'Updated successfully',
				1 => 'All fields are required',
 				2 => 'Country doesn\'t exists',
				3 => 'city doesn\'t exists',
 				4 => '',
				5 => 'Failed to update, please try again later',
				6 => 'delivery method not exists',
				7 => 'category not exists',
				8 => 'delivery_method_id must be numeric',
				9 => 'profile image  not valid',
				10 => 'phone number must be numeric',
				11 => 'phone number formate  invalid must strat with 5 or 05',
				12 => 'method delivery price required',
    			13 => 'delivery method must be an array',
 				14=> 'must choose at least one delivery method',
 				15 => 'provider not exists ',
 				16 => 'phone number used before'
 				
  			);
  			
  				$delivery_col = "delivery_methods.method_ar_name AS delivery_method";

 
		}

		$messages = array(
            
            'delivery_price.required'   => 12,
			'required'                  => 1,
 			'country_id.exists'         => 2,
 			'city_id.exists'            => 3,
 			'delivery_method_id.exists' => 6,
 			'category_id.exists'        => 7,
  			'mimes'                     => 9,
 			'numeric'                   => 10,
 			'regex'                     => 11,
 			'delivery_method_id.array'  => 13,
 			'delivery_method_id.min'     => 14,
 			'phone.unique'               => 16,
 		 

		);



		$rules=[

			'access_token'     =>  'required',
 			'full_name'        => 'required',
 			'store_name'       => 'required',
  			'longitude'        => 'required',
 			'latitude'         => 'required',
			'country_id'       => 'required|exists:country,country_id',
			'category_id'      => 'required|exists:categories,cat_id',
            'city_id'          => 'required|exists:city,city_id',
            'delivery_method_id'     =>'required|array|min:1|exists:delivery_methods,method_id',

 
		];

	
       $id = $this->get_id($request,'providers','provider_id');
       
        if($id == 0){
                 
                 
                 return response()->json(['status' => false, 'errNum' => 15, 'msg' => $msg[15]]);
                
            }

 
      $provider = DB::table("providers") 
                           ->where('provider_id',$id);
      


       $input = $request->only('full_name' , 'phone', 'city_id' ,'category_id','store_name','country_id','longitude','longitude');

         
        $input['country_code'] = $this -> checkCountryCodeFormate($request->input('country_code'));


        if($input['phone'] != $provider ->first()->  phone){

            $rules['phone'] = array('required','regex:/^(05|5)([0-9]{8})$/' ,'numeric','unique:providers,phone'); 
             $rules['country_code'] = "required";
            

        }else{

            $rules['phone'] = array('required','regex:/^(05|5)([0-9]{8})$/' ,'numeric');  
             $rules['country_code'] = "required";

        }

 
        if($request -> profile_pic){
 

            $rules['profile_pic'] = "required";
            $rules['image_ext']   = "required";

        } 


 $validator = Validator::make($request->all(), $rules ,$messages);

		if($validator->fails()){
			  $error = $validator->errors() -> first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

	if(in_array(2,$request -> delivery_method_id)){
               
                   $input['delivery_price']     = $request-> delivery_price;


                  if(!$request ->  delivery_price){

                      return response()->json(['status' => false, 'errNum' => 12, 'msg' => $msg[12]]);
                  }

                  if($request ->  delivery_price){

                  	if(!is_numeric($request ->  delivery_price)){

                             return response()->json(['status' => false, 'errNum' => 20, 'msg' => 'سعر التوصيل غير صحيح ' ]);

                  	}

                  }

		}
        
        
              DB::table('providers_delivery_methods') ->  where('provider_id',$id) -> delete();
              
		if($request -> delivery_method_id){
		    
		    
		    foreach($request -> delivery_method_id as $deliveryId){
		        
		        DB::table('providers_delivery_methods') -> insert([
		               
		               'provider_id'       =>   $id,
		               'delivery_method'   =>   $deliveryId
		            
		            ]);
		        
		    }
		   
		}
		
		 
 
     	
        
         if($input['phone'] != $provider ->first() ->  phone){

            $code = $this -> generate_random_number(4);

            $input['activate_phone_hash'] = json_encode([
                'code'   => $code,
                'expiry' => Carbon::now()->addDays(1)->timestamp,
            ]);

            $input['phoneactivated'] = "0";

            $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                $code . "رقم الدخول الخاص بك هو :- " ;

            (new SmsController())->send($message , $provider ->first()-> phone);

            $isPhoneChanged = true;
        }else{
            $isPhoneChanged = false;
            $code = "";
        }
 
 
        if($request-> profile_pic ){
 
             $image  = $request -> profile_pic ;
            if($provider ->first() -> profile_pic != null && $provider ->first() -> profile_pic != ""){
                    
		                //delete the previous image from storage 
		               if(Storage::disk('providers')->exists($provider ->first()  -> profile_pic))
		               {
		                     
		                     Storage::disk('providers')->delete($provider ->first()  -> profile_pic);

		               }
 

              //save new image    64 encoded
                    $image = $this->saveImage( $request -> profile_pic, $request->image_ext, 'providerProfileImages/');
                                       
                               
      					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 20, 'msg' => $errMsg]);
    					}else{
    					    
            					 
        
        	                      $input['profile_pic'] = $image;
    					}

 
            }

       
    }  
       
            $provider -> update($input);


          $getProvider  =  $provider -> first();

				$providerData = array(
					'id'              => $getProvider->provider_id,
					'provider_name'   => $getProvider->full_name,
					'store_name'      => $getProvider->store_name,
 					'phone' 		  => $getProvider->phone,
					'country_code'    => $getProvider->country_code,
 					'longitude'       => $getProvider->longitude,
					'latitude' 		  => $getProvider->latitude,
                    'commercial_photo'               =>env('APP_URL').'/public/providerProfileImages/'.$getProvider->commercial_photo,
					'membership_id' 		      => $getProvider->membership_id,
 					'category_id' 		          => $getProvider->category_id,
					'phoneactivated'              => $getProvider->phoneactivated,
					'status'                      => $getProvider->status, 
				    'publish'                     => $getProvider->publish, 
					'known_phone'                 => $getProvider->known_phone,
					'access_token'                => $getProvider->token,
					'delivery_price'              => $getProvider->delivery_price,
					'country_id'      => $getProvider->country_id,
					'city_id'         => $getProvider->city_id,
 					'provider_rate'   => $getProvider->provider_rate,
 					'profile_pic'     =>  env('APP_URL').'/public/providerProfileImages/'.$getProvider->profile_pic ,


 					'created_at'      => date('Y-m-d h:i:s', strtotime($getProvider->created_at))

				);
				
					$deliveries = DB::table("delivery_methods")->select('method_id AS delivery_id',$delivery_col,
																DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$getProvider->provider_id.') > 0, 1, 0) AS choosen'))
													   ->get();
													   


               //isPhoneChanged to notify mobile  app developers to redirect to activate phone number page 
  
           return response()->json([

           	     'status' => true, 
           	     'errNum' => 0, 
           	     'msg' => $msg[0] ,
           	     'provider' => $providerData,
           	     'isPhoneChanged' => $isPhoneChanged,
           	     'deliveries'     => $deliveries,
           	      'activation_code' => $code
           	     
 
           	 ]);
 
  	}


 

 
	       //provider categories functions 

	public function getProviderMainCats(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "categories.cat_ar_name AS cat_name";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "categories.cat_en_name AS cat_name";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

        
        $provider_id = $this->get_id($request,'providers','provider_id');
        
         if($provider_id == 0){
                 
                 return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
                
            }
            

		$selected = 'IF((SELECT count(provider_id) FROM providers WHERE category_id = categories.cat_id AND providers.provider_id = '.$provider_id .') > 0,1,0) AS choosen';
 

		$maincategory = DB::table('categories') 
						    -> where('categories.publish',1)
 						    ->select(
								    	'categories.cat_id', 
								    	$cat_col,
								    	DB::raw($selected) 
						            )
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'maincat' => $maincategory]);
	}


	public function getProviderStoreCategories(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "categories_stores.store_cat_ar_name AS store_cat_name";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "categories_stores.store_cat_en_name AS store_cat_name";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerCats = DB::table('providers') 
						    -> join('categories_stores','providers.provider_id','categories_stores.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
						    ->select('categories_stores.id AS cat_id', $cat_col)
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'cats' => $providerCats]);
	}

	 
 
public function addProviderCategory(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه التصنيف بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 =>  'فشل في اضافه التصنيف '
			);
		}else{
			$msg = array(
				0 => 'Category added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'Failed to add Category',
				 
			);
		}

		$messages = array(
			'required'       => 1,
			'access_token'   => 2
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'store_cat_ar_name'    => 'required',
			'store_cat_en_name'    => 'required',
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('store_cat_ar_name','store_cat_en_name');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
 

			try {
 

				$id=DB::table('categories_stores') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'cat_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}




public function editProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم التصنيف ',
				4 =>  'التصنيف غير موجود '
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'category id  required',
				4 => 'category not found ',

				 
			);
		}

		$messages = array(
						
			'required'                 => 1,
			'access_token.required'    => 2,
			'cat_id.required'          => 3,
			'cat_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('cat_id');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ])
                               -> select(
                               	           'id',
                               	           'store_cat_ar_name',
                               	           'store_cat_en_name'
                               
                                        ) 
                               -> first();



             if(!$cat){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $cat]);

 
		
	}

public function updateProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  التصنيف بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم التصنيف ',
				4 =>  'فشل في  تعديل  التصنيف ',
				5 => 'ألتصنيف غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Category updated successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'category id  required',
				4 => 'Failed to update Category',
				5 => 'Category not exists'

				 
			);
		}

		$messages = array(
			
			
			'required'                 => 1,
			'access_token.required'    => 2,
			'cat_id.required'          => 3,
			'cat_id.exists'            => 5

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'store_cat_ar_name'    => 'required',
			'store_cat_en_name'    => 'required',
			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('store_cat_ar_name','store_cat_en_name');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ]) ;


             if(!$cat -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $cat -> update($inputs);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}


	

public function deleteProviderCategory(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف  التصنيف بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم التصنيف ',
				3 =>  'فشل في  حذف   التصنيف ',
				4 => 'ألتصنيف غير موجود ',

			);
		}else{
			$msg = array(
				0 => 'Category delete successfully',
				1 => 'access_token required',
				2 => 'category id  required',
				3 => 'Failed to delete Category',
				4 => 'Category not exists'
				 
			);
		}

		$messages = array(
			
			
			'access_token.required'    => 1,
			'cat_id.required'          => 2,
			'cat_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'         => 'required',
			'cat_id'               => 'required|exists:categories_stores,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $cat = DB::table('categories_stores') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> cat_id

                                         ]) ;


             if(!$cat -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }





                
			try {
 
                   $cat -> delete();     

                   DB::table('products')-> where('category_id',$request -> cat_id) -> delete();

 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}


////////// provider offers apis ///////////////


	public function getProviderOffers(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "providers_offers.offer_title";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "providers_offers.offer_title";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerOffers = DB::table('providers') 
						    -> join('providers_offers','providers.provider_id','providers_offers.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
 						    ->whereExpire(0)
						    ->select(

						    	'providers_offers.id AS offer_id',
						    	 $cat_col,
						    	 DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo"),
						    	  'start_date',
						    	  'end_date',
						    	  'expire',
						    	  'providers_offers.publish',
						    	  'paid',
						    	  'paid_amount',
						    	  'providers_offers.status'
						    	  
						    	)
						    -> get();
 

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'offers' => $providerOffers]);
	}



public function addProviderOffer(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  العرض  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 => 'تاريخ  بدأ  ونهاية العرض لابد ان يكون علي الشكل  (yyyy-mm-dd H:i:s)' ,
				4 => 'صورة غير صالحة ',
				5 =>   'تاريح بدا العرض اكبر من تاريخ انتهاء العرض ',
				6 =>   'لابد ان يكون تاريج بدا العرض اكبر من او  يساوي تاريخ اليوم ' ,
				7 =>  'فشل في اضافه  العرض ',
				8 => 'المدينة غير موجوده ',
			);
		}else{
			$msg = array(
				0 => 'Offer added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>  'start and end date  must be in format (yyyy-mm-dd H:i:s)',
				4 =>  'image not valid' ,
				5 => 'start date greater than end date ',
				6 =>  'start date of the offer must greater  than or equal to  today',
				7 =>'Failed to add Offers',
				8 => 'City not exists'
				 
			);
		}

		$messages = array(

			'required'                => 1,
			'access_token.required'   => 2,
			'date_format'             => 3,
			'mimes'                   => 4,
			'after'                   => 5,
			'after_or_equal'          => 6,
			'city_id.exists'          => 8,

		);

         $rules=[

			'access_token'         => 'required',
			'image_ext'            => 'required',
			'offer_title'          => 'required',
 			'start_date'           => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d'),
			'end_date'             => 'required|date_format:Y-m-d H:i:s|after:start_date',
			'photo'                => 'required',
			'city_id'              => 'required|exists:city,city_id'
 
		    ];


		$validator = Validator::make($request->all(),$rules, $messages);

 
		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('offer_title','start_date','end_date','city_id');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
             

              if($request-> photo ){


			           /* $image  = $request -> photo ;

						//save new image   
						$image ->store('/','offers');

						$nameOfImage = $image ->hashName();

						$inputs['photo'] =  $nameOfImage;*/
						
						
                   //save new image   64 encoded
                     
                                
                       $image = $this->saveImage($request -> photo,$request -> image_ext, 'offers/');
                                 
      					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['photo']  = $image;
    					}
    					
    					
			    }  

 
           
			try {
 

				$id=DB::table('providers_offers') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'offer_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
			}
		
	}


public function editProviderOffer(Request $request){

        	$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم   العرض  ',
				4 =>  ' العرض  غير موجود '
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'offer id  required',
				4 => 'offer not found ',
 
				 
			);
		}

		$messages = array(
				 
			'access_token.required'     => 2,
			'required'                  => 1,
			'offer_id.required'         => 3,
			'offer_id.exists'           => 4,


		);

		$validator = Validator::make($request->all(),[
			'access_token'           => 'required',
 			'offer_id'               => 'required|exists:providers_offers,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

          
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id

                                         ])
                               -> select(
                               	           'id AS offer_id',
                               	           'offer_title',
                               	           'paid',
                               	           'status',
                               	           'publish',
                               	           'start_date',
                               	           'end_date',
                               	           DB::raw("CONCAT('". url('/') ."','/offers/',providers_offers.photo) AS offer_photo")                               
                                        ) 
                               -> first();


              if(!$offer){
 
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


             if($offer -> paid == "1" && $offer -> status == "2" && $offer -> publish == "1" )
             {

                unset($offer -> start_date);
                unset($offer -> end_date);

             }

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $offer]);

 
}

public function updateProviderOffer(Request $request){

   	$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  العرض  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم   العرض  ',
				4 =>  'عنوان العرض مطلوب',
				5 => ' العرض  غير موجوده ',
				6 => 'تاريخ  بدأ  ونهاية العرض لابد ان يكون علي الشكل  (yyyy-mm-dd H:i:s)' ,
				7 => 'عنوان  العرض لابد ان يكون احرف ',
				8 => 'اقصي عدد مسموح به من الاحرف  للعنوان هو 200',
				9 => 'تاريخ بدا وانتهاء العرض  مطلوب',
				10 => 'فشل في تحديث العرض '
				
			);
		}else{
			$msg = array(
				0 => 'Offer updated successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'offer id  required',
				4 => 'Offer Title required',
				5 => 'Offer Not found',
				6 =>  'Start and end date  must be in format (yyyy-mm-dd H:i:s)',
				7 => 'Offer title must be string',
				8 => 'Title max character is 200 Char',
				9 => 'offer start and end date required',
				10=> 'Faild to update offer'
  
			);
		}

		$messages = array(
						
 			'access_token.required'      => 2,
			'offer_id.required'          => 3,
			'offer_title.required'       => 4,
			'offer_id.exists'            => 5,
			'start_date.date_format'     => 6,
			'end_date.date_format'       => 6,
			'offer_title.string'         => 7,
			'offer_title.max'            => 8,
			'start_date.required'        => 9,
			'end_date.required'          => 9

		);

       $rules=[
			
			'access_token'           => 'required',
			'offer_id'               => 'required|exists:providers_offers,id', 
			'offer_title'            => 'required|string|max:200',			
			'start_date'             =>'date_format:Y-m-d H:i:s',
			'end_date'               =>'date_format:Y-m-d H:i:s'
			
		];   


		if($request -> start_date){

			$rules['start_date']  ='required|date_format:Y-m-d H:i:s';
			$rules['end_date']    ='required|date_format:Y-m-d H:i:s';
			$inputs['start_date'] = $request -> start_date;
		}

		if($request -> end_date){

			$rules['end_date']   ='required|date_format:Y-m-d H:i:s';
			$rules['start_date'] ='required|date_format:Y-m-d H:i:s';
			$inputs['end_date']  = $request -> end_date;
		}
 

		$validator = Validator::make($request->all(),$rules,$messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

         

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
         $inputs['offer_title']  =  $request -> offer_title;


         $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id

                                         ]) ;
        
            if(!$offer -> first()){
 
                return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
             }

          if($request-> photo ){

			           /* $image  = $request -> photo ;
						//save new image   
						$image ->store('/','offers');
						$nameOfImage = $image ->hashName();
						$inputs['photo'] =  $nameOfImage;*/
                        //save new image   64 encoded
 

             $image = $this->saveImage($request -> photo,$request -> image_ext, 'offers/');
             $name = $offer -> first() -> photo;

	               if(Storage::disk('offers')->exists($name))
	               {	                     
	                    Storage::disk('offers')->delete($name);
	               }         
      					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['photo']  = $image;
    					}	
			    }  

                
			try {
 
                   $offer -> update($inputs);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}

}


	public function payProviderOffer(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم   تغيير حاله العرض الي مدفوع ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  العرض ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   تغيير حالة الطلب  ',
				5 => 'العرض غير موجود ',
				6 => 'الكميه المدفوعه غير صحيحه',
			);
		}else{
			$msg = array(
				0 => 'offer status changed successfully',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to change status',
				5 =>  'Offer not exists',
				6 => 'money is invalid',
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'offer_id.required'          => 2,
			'required'                   => 3,
			'offer_id.exists'            => 5,
			'regex'                      => 6,
			

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'offer_id'               => 'required|exists:providers_offers,id' ,
			'paid_amount'            => 'required|regex:/^\d+(\.\d{1,2})?$/'
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id
                                         ]) ;


             if(!$offer -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $offer -> update([
                           

                             'paid'          => '1',
                             'paid_amount'   => $request -> paid_amount,
                             'status'        => '2'

                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}

 

	public function stopProviderOffer(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  ايقاف العرض ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  العرض ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   ألعرض  ',
				5 =>  'لا يمكنك ايقاف الطلب قبل موافقه الاداره علية اولا ',
				6 => 'العرض غير موجود '
			);
		}else{
			$msg = array(
				0 => 'offer  stoped successfully',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to stop offer',
				5 => 'cant\'t  stop offers that not accepted from admin ',
				6 => 'offer not exists'
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'offer_id.required'          => 2,
			'required'                   => 3,
			'offer_id.exists'            => 6

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'offer_id'               => 'required|exists:providers_offers,id' ,
 			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $offer = DB::table('providers_offers') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> offer_id
                                         ]) ;


             if(!$offer -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


             if($offer -> first() -> publish == 0 ){


             	  return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
             }


                
			try {
 
                   $offer -> update([
                           

                             'publish'          => '0',
                             

                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}



	  //provider product APIS

	public function addProduct(Request $request){



 		$lang = $request->input('lang');


	 if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه المنتج بنجاح ',
				1 => 'جميع الحقول مطلوبة ',
				2 => 'السعر لابد ان يكون ارقام ',
				3 => ' القسم المختار غير موجود ',
				4 => ' الصور لابد ان تكون علي هيئة  مصفوفه ',
				5 => 'امتداد الصوره غير مسموح به ',
				6 => 'التفضيلات لابد ان  تكون علي شكل مصفوفه ',
				7 => ' الاحجام  لابد ان  تكون علي شكل مصفوفه ',
				8 => ' الالوان  لابد ان  تكون علي شكل مصفوفه ',
				9 =>  ' سعر الاضافات مطلوب في حال وجود اضافه ',
				10 => 'سعر الحجم  مطلوب  في حال وجود احجام ',
				11 => 'سعر اللون  مطلوب في حال وجود الوان ',
				12 => ' فشل في رفع الصوره من فضلك حاول مجددا ',
				13 => 'فشل في اضافه المنتج من فضلك حاول مجددا ',
				14 => 'access_token not found',
				15 => 'رقم المتجر غير موجود ',
				16 => 'ألتصنيف غير موجود ' ,
				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
				24 => 'امتدادات الصور يجب ان تكون علي شكل مصفوفة',
				25 => 'عدد الصور لا يساوي عدد الامتدادات ',
				
			);
		}else{
			$msg = array(
				0 => 'Product added successfully',
				1 => 'All fields are required',
				2 => 'price must be a number',
				3 => 'category_id not exists',
				4 => 'product images must be array ',
				5 => 'image extension not allowed',
				6 => 'options must be array',
				7 => 'sizes must be array',
				8 => 'colors must be array',
				9 => 'option price  required when option added',
				10 => 'size price  required when sizes added',
				11 => 'color price  required when colors added',
				12 => 'Failed to upload image, please try again later',
				13 => 'Failed to add the product , please try again later',
				14 => 'access_token not found',
				15 => 'store does\'t exist' ,
				16 => ' category not exists' ,
				17 => 'max image size is 20000' ,
				18 => 'options must be at least one option' ,
				19 => 'sizes must be at least one size' ,
				20 => 'colors must be at least one color' ,
				21 => 'options prices must pass as array ' ,
				22 => ' sizes prices must pass as array ' ,
				23 => 'colors prices must pass as array' ,
				24 => 'image_ext must be array ',
				25 => 'images and its extensions not equal'
				

			
				 
			);
		}

		$messages = array(
			'access_token.required'    => 14,
			'required'                 => 1,
			'numeric'                  => 2,
			'exists'                   => 3,
			'product_images.array'     => 4,
			'mimes'                    => 5,
			'options.array'            => 6,
			'sizes.array'              => 7,
			'colors.array'             => 8,
			'options_price.required_with' => 9,
			'sizes_price.required_with'   => 10,
			'colors_price.required_with'  => 11,
			'category_id.exist'           => 16,
			'max'                         => 17,
			'options.min'                 => 18,
			'sizes.min'                   => 19,
			'colors.min'                  => 20,
			'options_price.array'         => 21,
			'sizes_price.array'           => 22,
			'colors_price.array'          => 23,
			'image_ext.array'             => 24,

			   


		);

		$validator = Validator::make($request->all(),[
			'access_token'   => 'required',
 			'title'          => 'required',
			'category_id'    => 'required|exists:categories_stores,id',
			'description'    => 'required',
 			'price'          => 'required|numeric',
 			'product_images' => 'required',
 			'product_images'   => 'required|array',
            //'product_images.*' => 'required',  that means all of them must pass value
            'image_ext'        => 'required|array',
            'image_ext.*'      => 'required',
            'options'         => 'array|min:1',
            'options_price'   => 'array|required_with:options',
            'sizes'   => 'array|min:1',
            'sizes_price'   => 'array|required_with:sizes',
            'colors'  => 'array|min:1',
            'colors_price'   => 'array|required_with:colors'


 		],$messages);

		if($validator->fails()){
		      $error = $validator->errors()-> first()  ;

		   return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

		
       $provider_id = $this->get_id($request,'providers','provider_id');
       
          if($provider_id == 0){
                 
                 
                 return response()->json(['status' => false, 'errNum' => 14, 'msg' => $msg[14]]);
                
            }
      
       $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);

          if(! $provider -> first()){
 
                  return response()->json(['status'=> false, 'errNum' => 15, 'msg' => $msg[15]]);

          }                 
       
       $id =0; 

       $id = DB::table('products') -> insertGetId([
                        
                        'provider_id'    => $provider_id,
						'title'          => $request  ->  title,
						'category_id'    => $request  ->  category_id,
						'price'          => $request  ->  price,
						'description'    => $request  ->  description,

 					]);

        if($id ==0 )
        {
           
            return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
        	
        }


 		/*	if( $request -> hasFile('product_images')){
 
  
				foreach($request->  product_images  AS $image){
  
                   //save new image   
                    $image ->store('/','products');
                                       
                   $nameOfImage = $image ->hashName();

                     DB::table('product_images') -> insert([

                     	  'image'      => $nameOfImage,
                     	  'product_id' => $id

                     ]);
 
 
					} 
 	  
				}*/
				
				
				
				if( $request -> has('product_images')){
 
  
  
                 $image_extensions = $request -> image_ext;
                 $products_images  = $request -> product_images;
                 
                 
                  $extensions = array_filter($image_extensions,function($ext){

                               return !empty($ext);

			            });
			            
			            
			      $images = array_filter($products_images,function($images){

                               return !empty($images);

			            });
			            
                 
                 
                 if(count($extensions) != count($images)){
                     
                     return response()->json(['status'=> false, 'errNum' => 25, 'msg' => $msg[25]]);
                 }
  
				foreach($images  AS $index =>  $image){
   
                   
                    //save new image   64 encoded
                     
                                
                      $image = $this->saveImage($image,$extensions[$index], 'products/');
                                 
      					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $nameOfImage= $image;
    					}
    					
    				 

                     DB::table('product_images') -> insert([

                     	  'image'      => $nameOfImage,
                     	  'product_id' => $id

                     ]);
 
 
					} 
 	  
				}


           try {

                  //store options and its price if availble 

				    if( $request -> has('options')){
   
				    	$this -> storeOptionsWithPrices($request -> options ,$request -> options_price,$id);
   
                     }



                      //store sizes and its price if availble 

				    if( $request -> has('sizes')){
   
				    	$this -> storeSizesWithPrices($request -> sizes ,$request -> sizes_price,$id);
   
                     }

 
                      //store options and its price if availble 

				    if( $request -> has('colors')){
   
				    	$this -> storeColorsWithPrices($request -> colors ,$request -> colors_price,$id);
   
                     }

               } catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 13, 'msg' => $msg[13]]);
			}
			      

          return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0] ,'product_id' => $id]);
 
 
}

 
public function getProducts(Request $request){

   $lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

			$cat_col = "products.title";
			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

			$cat_col = "products.title";
		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}



    //products with last inserted image
 

		  $products = DB::table('providers') 
		                    -> join('products','providers.provider_id','=','products.provider_id')
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
 						    ->where('products.publish',1)
						    ->select(

							    	'products.id AS product_id',
							    	 $cat_col,
							    	 'products.description',
							    	 'providers.provider_id',
							    	 'products.price'

						    	   
						    	)
						    -> paginate(10);
						
						
						    
	   if(isset($products) && $products -> count() > 0)  {
	       
	       foreach($products as $product){
	           
	           
	            $mainImage = DB::table('product_images') -> where('product_id',$product -> product_id) ->select('image') ->  first();  // get onlly the first image as main image of product
	            if($mainImage){

	            	 $product -> product_image = $mainImage -> image ? env('APP_URL').'/public/products/'.$mainImage -> image : "";

	            }else{

                   $product -> product_image = "";
	            }
	            
 	       }
	   }
 

  return response()->json(['status' => true, 'errNum' => 0, 'msg' => '','product' => $products]);
 

}



public function delete_Product(Request $request){
       

       $lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف   المنتج بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  المنتج  ',
				3 =>  'فشل في  حذف   المنتج  ',
				4 => 'هذا المنتج غير موجود '
			);
		}else{
			$msg = array(
				0 => 'Product delete successfully',
				1 => 'access_token required',
				2 => 'Product id  required',
				3 => 'Failed to delete Product',
				4 => 'this product doen\'t exists'
				 
			);
		}

		$messages = array(
			
			
			'access_token.required'        => 1,
			'product_id.required'          => 2,
			'exists'                       => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'            => 'required',
			'product_id'               => 'required|exists:products,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
               
              $product = DB::table('products') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> product_id

                                         ]) ;


             if(!$product -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }


                
			try {
 
                   $product -> delete();   

                   DB::table('product_images') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_options') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_sizes') -> where('product_id',$request -> product_id) -> delete();
                   DB::table('product_colors') -> where('product_id',$request -> product_id) -> delete();
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
 
  
}




public function prepare_Product_Update(Request $request){

 		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  المنتج  ',
				4 =>  ' المنتج  غير موجود ',
				5 => 'المتجر غير موجود '
			);


			$cat_col = 'categories_stores.store_cat_ar_name as cat_name';


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'product id  required',
				4 => 'product not found ',
				5 => 'provider not found '
				 
			);

			$cat_col = 'categories_stores.store_cat_en_name as cat_name';
		}

		$messages = array(
						
			'required'                        => 1,
			'access_token.required'           => 2,
			'product_id.required'             => 3,
			'product_id.exists'               => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'             => 'required',
 			'product_id'               => 'required|exists:products,id' 
			
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
         

          $provider_id = $this->get_id($request,'providers','provider_id');

          $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);

       if(!$provider){
               
           return response()->json(['status' => false, 'errNum' =>5 , 'msg' => $msg[5]]);
       }


 
		$selected = 'IF((SELECT count(id) FROM products WHERE category_id = categories_stores.id AND products.id = '.$request-> product_id .') > 0,1,0) AS choosen';


		$product = DB::table('products') 
		             -> where('products.id', $request-> product_id)
					 ->join('providers', 'products.provider_id', '=', 'providers.provider_id')
					 -> where('providers.provider_id',$provider_id)
					 ->select(
					 	      'products.id as product_id',
					 	      'products.title', 
					 	      'products.description',
					 		  'products.price')
					 ->first();


		$images = DB::table('product_images')
		                 ->where('products.id', $request-> product_id)
						 ->join('products', 'product_images.product_id','products.id')
						 ->select( 'product_images.id AS  image_id',
 						 	        DB::raw("CONCAT('". url('/') ."','/products/',product_images.image) AS product_image")
						 	)->get();


		      /*   if(isset($images) &&  $images -> count() > 0 ){
 
                       foreach ($images as $key => $image) {
                       	  
                       	    $type = pathinfo($image -> product_image, PATHINFO_EXTENSION);
							$data = file_get_contents($image -> product_image);
							$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

							$image -> product_image = $base64;
                       }
                 
		         }*/
  
		$categories = DB::table('categories_stores') 
		                         ->join('providers','categories_stores.provider_id','providers.provider_id')
		                         ->where('categories_stores.provider_id',$provider_id)
		                         ->select(
		                         	       'categories_stores.id AS cat_id',
		                         	       $cat_col,
		                         	       DB::raw($selected)


		                                 )
		                         -> get();

        $options  = DB::table('product_options')  -> where('product_id',$request-> product_id) -> select('id','name','price') ->get();

        $sizes  = DB::table('product_sizes')  -> where('product_id',$request-> product_id) -> select('id','name','price') ->get();

        $colors  = DB::table('product_colors')  -> where('product_id',$request-> product_id) -> select('id','name','price') ->get();
 

		return response()->json([
								 'status'   => true,
								 'errNum'   => 0,
								 'msg'      => '',
								 'product'  => $product,
								 'images'   => $images,
								 'cats'     => $categories,
								 'options'  => $options,
								 'sizes'    => $sizes,
								 'colors'   => $colors,

								]);
         
}
 


public function updateProduct(Request $request){

		$lang = $request->input('lang');
 
	 if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  المنتج بنجاح ',
				1 => 'جميع الحقول مطلوبة ',
				2 => 'السعر لابد ان يكون ارقام ',
				3 => ' القسم المختار غير موجود ',
				4 => ' الصور لابد ان تكون علي هيئة  مصفوفه ',
				5 => 'امتداد الصوره غير مسموح به ',
				6 => 'التفضيلات لابد ان  تكون علي شكل مصفوفه ',
				7 => ' الاحجام  لابد ان  تكون علي شكل مصفوفه ',
				8 => ' الالوان  لابد ان  تكون علي شكل مصفوفه ',
				9 =>  ' لابد من ادحال سعر التفضيلات   في حال وجود تفضيلات ',
				10 => 'سعر الحجم مطبوي في حال وجود احجام ',
				11 => 'سعر اللون  مطلوب في حال وجود الوان ',
				12 => ' فشل في رفع الصوره من فضلك حاول مجددا ',
				13 => 'فشل في  تعديل  المنتج من فضلك حاول مجددا ',
				14 => 'توكن المستخدم غير موجود ',
				15 => ' رقم المتجر غير موجود ' ,
				16 => 'الصور المحذوفه لابد ان تكون مرره علي شكل مصفوفه ',

				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
				24 => 'ألتصنيف غير موجود ' ,
				25 => 'امتدادات الصوره لابد ان تكون علي شطل مصفوفه ',
				
			);
		}else{
			$msg = array(
				0 => 'Product updated successfully',
				1 => 'All fields are required',
				2 => 'price must be a number',
				3 => 'category_id not exists',
				4 => 'product images must be array ',
				5 => 'image extension not allowed',
				6 => 'options must be array',
				7 => 'sizes must be array',
				8 => 'colors must be array',
				9 => 'option price  required when option added',
				10 => 'size price  required when sizes added',
				11 => 'color price  required when colors added',
				12 => 'Failed to upload image, please try again later',
				13 => 'Failed to update the product , please try again later',
				14 => 'access_token not found',
				15 => 'store does\'t exist' ,
				16 => 'deleted images must be array ',
				17 => ' اقصي حجم مسموح به في الصور هو 20000' ,
				18 => 'المفضلات لابد ان تكون ضافه وحده علي الاقل  ' ,
				19 => 'الحجوم لابد ان تكون تكون حجم واخد علي الاقل ' ,
				20 => 'الالوان لابد ان تكون تكون لون واحد ع الاقل ' ,
				21 => 'اسعار المفضلات لابد ان تكون مصفوفه ' ,
				22 => ' اسعار الحجوم لابد ان تكون مصفوفه ' ,
				23 => ' اسعار الالوان لابد ان تكون مصفوفه ' ,
				24 => 'ألتصنيف غير موجود ' ,
				25 => 'images extensions must be array ',
				 
				 
			);
		}

		$messages = array(
			'access_token.required'    => 14,
			'required'                 => 1,
			'numeric'                  => 2,
			'exists'                   => 3,
			'product_images.array'     => 4,
			'mimes'                    => 5,
			'options.array'            => 6,
			'sizes.array'              => 7,
			'colors.array'             => 8,
			'options_price.required_with' => 9,
			'sizes_price.required_with'   => 10,
			'colors_price.required_with'  => 11,
			'deleted_images.array'        => 16,
			'image_ext.array'             => 25,

			'max'                         => 17,
			'options.min'                 => 18,
			'sizes.min'                   => 19,
			'colors.min'                  => 20,
			'options_price.array'         => 21,
			'sizes_price.array'           => 22,
			'colors_price.array'          => 23,
			'category_id.exist'           => 24

		);

		$validator = Validator::make($request->all(),[

			'access_token'     => 'required',
 			'title'            => 'required',
			'category_id'      => 'required|exists:categories_stores,id',
			'product_id'       => 'required|exists:products,id',  
			'description'      => 'required',
 			'price'            => 'required|numeric',
 			
  			'product_images' => 'required',
 			'product_images'   => 'required|array',
          //  //'product_images.*' => 'required',  that means all of them must pass value
            'image_ext'        => 'required|array',
            'image_ext.*'      => 'required',
            
            
            'options'          => 'array|min:1',
            'options_price'    => 'array|required_with:options',
            'sizes'            => 'array|min:1',
            'sizes_price'      => 'array|required_with:sizes',
            'colors'           => 'array|min:1',
            'colors_price'     => 'array|required_with:colors',
            'deleted_images'   => 'nullable'


 		],$messages);

		if($validator->fails()){
		      $error = $validator->errors()-> first()  ;

			 
		   return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
    

          
      $provider_id = $this->get_id($request,'providers','provider_id');
      
        
          if($provider_id == 0){
                 
                 
                 return response()->json(['status' => false, 'errNum' => 14, 'msg' => $msg[14]]);
                
            }
            
            

       $provider    = DB::table("providers") 
                           ->where('provider_id',$provider_id);
       $id          = $request -> product_id;                    

          if(! $provider -> first()){
 
                  return response()->json(['status'=> false, 'errNum' => 15, 'msg' => $msg[15]]);

          }        


              // delete array of image from database and storage 
 
               if($request->has('deleted_images')){

               	        //remove null values if exists 
 
               	        $deleted_images = array_filter($request -> deleted_images,function($deleted_images){

                                 $image_exists = DB::table('product_images') -> where('id',$deleted_images);


                                if(!empty($deleted_images) && $image_exists){


                                	return $deleted_images;
                                }

			            });
  
                         
                        // DB::table('product_images') ->whereIn('id',$deleted_images)->delete();
                         

                        foreach ($deleted_images as $key => $images) {
                        	   
		                      $image =  DB::table('product_images')
						         ->where('id',$images) -> first();


                             if($image){
    
						       $name = $image ->  image;
						         
						       //delete from storage to space disk free 

						       if(Storage::disk('products')->exists($name))
					               {
					                     
					                    Storage::disk('products')->delete($name);

					               }

                                  //delete from database 
						       $image -> delete();  
						       
						       
                            }       
  
                        }
				       
			    }
			     
			    
          $data=$request -> only('title','description','category_id','description','price');

 		  $data['provider_id']    =  $provider_id;               


             //delete previous images
 		   $image =  DB::table('product_images')
						         ->where('product_id',$request -> product_id)
						         ->delete();


	
				if( $request -> has('product_images')){
 
   
                 $image_extensions = $request -> image_ext;
                 $products_images  = $request -> product_images;
                 
                 
                  $extensions = array_filter($image_extensions,function($ext){

                               return !empty($ext);

			            });
			            
			            
			      $images = array_filter($products_images,function($images){

                               return !empty($images);

			            });
			            
                 
                 
                 if(count($extensions) != count($images)){
                     
                     return response()->json(['status'=> false, 'errNum' => 25, 'msg' => $msg[25]]);
                 }
  
				foreach($images  AS $index =>  $image){
   
                   
                    //save new image   64 encoded
                     
                                
                      $image = $this->saveImage($image,$extensions[$index], 'products/');
                                 
      					
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $nameOfImage= $image;
    					}
    					
    				 

                     DB::table('product_images') -> insert([

                     	  'image'      => $nameOfImage,
                     	  'product_id' => $id

                     ]);
 
 
					} 
 	  
				}
 
           try {

                  //store options and its price if availble 

				    if( $request -> has('options')){

				    	 //delete  old options then  store 
				    	   DB::table('product_options') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeOptionsWithPrices($request -> options ,$request -> options_price,$id);
   
                     }

 
                      //store sizes and its price if availble 

				    if( $request -> has('sizes')){

				    	 DB::table('product_sizes') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeSizesWithPrices($request -> sizes ,$request -> sizes_price,$id);
   
                     }

 
                      //store options and its price if availble 

				    if( $request -> has('colors')){

				    	DB::table('product_colors') -> where('product_id',$id) ->  delete();
   
				    	$this -> storeColorsWithPrices($request -> colors ,$request -> colors_price,$id);
   
                     }

                     DB::table('products') -> where('id',$id) -> update($data);

               } catch (Exception $e) {

				return response()->json(['status'=> false, 'errNum' => 13, 'msg' => $msg[13]]);

			}
			   
                     
  

       return response()->json(['status'=> true, 'errNum' => 0, 'msg' =>$msg[0]]);

		 
	}




public function storeOptionsWithPrices($optionsreq , $options_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $options = array_filter($optionsreq,function($options){

                               return !empty($options);

			            });

			 $options_price = array_filter($options_pricereq,function($options_price){

                               return !empty($options_price);

			            });
 

				if (count($options)) {

						foreach ($options as $index => $option) {

							//get the  price by index of option name 

							$price = isset($options_price[$index]) ? $options_price[$index] : 0;

                             
                                DB::table('product_options') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $option,
                                      'price'        =>  $price

                                   ]);

						}
				}

}




public function storeSizesWithPrices($sizesreq , $sizes_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $sizes = array_filter($sizesreq,function($sizes){

                               return !empty($sizes);

			            });

			 $sizes_price = array_filter($sizes_pricereq,function($sizes_price){

                               return !empty($sizes_price);

			            });
 

				if (count($sizes)) {

						foreach ($sizes as $index => $size) {

							//get the  price by index of option name 

							$price = isset($sizes_price[$index]) ? $sizes_price[$index] : 0;

                             
                                DB::table('product_sizes') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $size,
                                      'price'        =>  $price

                                   ]);

						}
				}

}




public function storeColorsWithPrices($colorsreq , $colors_pricereq , $product_id) {


              // because there are array of options passed to request and my be one of index is null so we filter by remove it 


			 $colors   = array_filter($colorsreq,function($colors){

                               return !empty($colors);

			            });

			 $colors_price = array_filter($colors_pricereq,function($colors_price){

                               return !empty($colors_price);

			            });
 

				if (count($colors)) {

						foreach ($colors as $index => $color) {

							//get the  price by index of option name 

							$price = isset($colors_price[$index]) ? $colors_price[$index] : 0;

                             
                                DB::table('product_colors') -> insert([
                                      
                                      'product_id'   =>  $product_id ,
                                      'name'         =>  $color,
                                      'price'        =>  $price

                                   ]);

						}
				}

}





         //// provider jobs functions /////////

public function providerJobs(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				 
			);

 			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
				 
			);

 		}

		$messages = array(
			'access_token.required'   => 1,
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}


 

		$providerJobs = DB::table('providers') 
						    -> join('provider_jobs','providers.provider_id','provider_jobs.provider_id') 
 						    ->where('providers.provider_id',$this->get_id($request,'providers','provider_id'))
						    ->select('provider_jobs.id AS job_id','job_title','job_desc')
						    -> get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'jobs' => $providerJobs]);
	}

	 
 
public function addProviderJob(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  الوظيفة  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 =>  'فشل في اضافه   الوظيفة  ',
				4 => 'اقصي عدد احرف للعنوان هو 200 حرف والوصف هو 5000 حرف'
			);
		}else{
			$msg = array(
				0 => 'Job added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'Failed to add Job',
				4 => 'max job title is 200 char and discription is 5000 char'
				 
			);
		}

		$messages = array(
			'required'                 => 1,
			'access_token.rrequired'   => 2,
			'max'                      => 4
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'job_title'            => 'required|max:200',
			'job_desc'             => 'required|max:50000',

			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('job_title','job_desc');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
 

			try {
 

				$id=DB::table('provider_jobs') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'job_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}




public function editProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود '
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found '
				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3

		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'job_id'               => 'required' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

          
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ])
                               -> select(
                               	           'id',
                               	           'job_title',
                               	           'job_desc'
                               
                                        ) 
                               -> first();



             if(!$job){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $job]);

 
		
	}

public function updateProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تعديل  الوظيفة  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  'فشل في  تعديل  الوظيفة  ',
				5 => 'الوظيفه غير موجوده ',
				6 =>'عنوان  الوظيفة لابد ان يكون احرف ',
				7 =>'اقصي عدد مسموح به من الاحرف  للعنوان هو 200',
				8 =>'الوصف لابد الا يتجاوز 5000 حرف',
			);
		}else{
			$msg = array(
				0 => 'Job updated successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 => 'job id  required',
				4 => 'Failed to update job',
				5 => 'Job Not found',
				6 => 'job title must be string',
				7 =>  'title max character i 200 Char',
				8 =>  'job description max characters is 5000'  



				 
			);
		}

		$messages = array(
			
			
			'required'                 => 1,
			'access_token.required'    => 2,
			'job_id.required'          => 3,
			'job_id.exists'            => 5,
			'job_title.string'         => 6,
			'job_title.max'            => 7,
			'job_desc.max'             => 8,

			


		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
			'job_title'            => 'required|string|max:200',
			'job_desc'             => 'required|max:5000',
			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('job_title','job_desc');


         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ]) ;


             if(!$job -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $job -> update($inputs);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}


	

public function deleteProviderJob(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  حذف   الوظيفة  بنجاح ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  الوظيفة  ',
				3 =>  'فشل في  حذف    الوظيفة  ',
				4 => 'الوظيفة غير موجودة '
			);
		}else{
			$msg = array(
				0 => 'Job delete successfully',
				1 => 'access_token required',
				2 => 'Job id  required',
				3 => 'Failed to delete job',
				4 => 'Job not Found'

				 
			);
		}

		$messages = array(
			
			
			'access_token.required'    => 1,
			'job_id.required'          => 2,
			'job_id.exists'            => 4

		);

		$validator = Validator::make($request->all(),[

			'access_token'         => 'required',
			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

         $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> job_id

                                         ]) ;
                                         


             if(!$job -> first()){
 
                return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
             }





                
			try {
 
                  DB::table('applicants') -> where('job_id',  $request -> job_id) -> delete();     
                   $job -> delete();     
                   
                 
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		
	}

 

public function getJobDetails(Request $request){

    
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found ',

				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3,
			'job_id.exists'            => 4


		);

		$validator = Validator::make($request->all(),[
  			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
             
             $user_id =0;
            
            if($request -> access_token){
                
                  $user_id = $this->get_id($request,'users','user_id');
                 
            }

       

              $job = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_jobs.id' =>   $request -> job_id

                                         ])
                               -> join('providers','providers.provider_id','provider_jobs.provider_id')         
                               ->leftjoin('applicants','provider_jobs.id','=','applicants.job_id')
                               -> select(
                               	           'provider_jobs.id As job_id',
                               	           'job_title',
                               	           'job_desc',
                               	           'providers.store_name',
                               	           'providers.provider_id',
                               	           DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',providers.profile_pic) AS store_image"),
                               	           DB::raw('DATE(provider_jobs.created_at) AS created_date'),
                               	           DB::raw('COUNT(applicants.job_id) AS applicants'),
                               	           DB::raw('IF(applicants.user_id = '.$user_id.', true, false) AS isCurrentUserApplied')
                               	           
                               
                                        ) 
                               -> first();


            
 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $job]);
 
}



public function jobApplicants(Request $request){


		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  الوظيفة  ',
				4 =>  ' الوظيفة  غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'Job id  required',
				4 => 'Job not found ',

				 
			);
		}

		$messages = array(
						
			
			'access_token.required'    => 2,
			'required'                 => 1,
			'job_id.required'          => 3,
			'job_id.exists'            => 4


		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'job_id'               => 'required|exists:provider_jobs,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
  
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
 
              $applicants = DB::table('provider_jobs') 
                                -> where ([
                                	         'provider_id'      =>   $inputs['provider_id'],
                                	         'provider_jobs.id' =>   $request -> job_id

                                         ])
                               ->leftjoin('applicants','provider_jobs.id','=','applicants.job_id')
                               -> select(
                               	           'applicants.id AS applicant_id',
                               	           'applicants.name AS applicant_name',
                               	           DB::raw("CONCAT('".env('APP_URL')."','/public/cvs/', applicants.cv) AS applicant_cv"),
                               	           DB::raw('DATE(applicants.created_at) AS created_date')
                                
                                        ) 
                               -> paginate(10);

 
  
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'applicants' => $applicants]);
 

}



 ///////excellent requests functions /////////////////



public function getExcellenceRequests(Request $request){
		$lang = $request->input('lang');
         
         	if($lang == "ar"){
			$msg = array(
				 
				1 => 'توكن المستخدم غير موجود ',
				2 => 'لابد من تحديد نوع الطلبات  0 or 1',
				 
			);

 			
		}else{
			$msg = array(
 				1 => 'provider access_token is required',
 				2 => 'type field required_with',
				 
			);

 		}

		$messages = array(
			'access_token.required'   => 1,
			'type.required'           => 2,
			'type.in'                 => 2
					);

		$validator = Validator::make($request->all(),[
			'access_token'  => 'required',
			'type'          => 'required|in:0,1'
			 
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

           
           $provider_id = $this->get_id($request,'providers','provider_id');


           $conditions=[];

           array_push($conditions, ['providers.provider_id', '=', $provider_id]);

           if($request -> type == 0)
             array_push($conditions, ['excellence_requests.status', '=', '0']);
            if($request -> type == 1)
            array_push($conditions, ['excellence_requests.status', '=', '1']);	
 
     
		$providerRequests= DB::table('providers') 
						    -> join('excellence_requests','providers.provider_id','excellence_requests.provider_id') 
						    -> where($conditions)
 						    ->select(

						    	  'excellence_requests.id AS request_id',    	   	 
						    	  'paid',
						    	  'paid_amount',
						    	  'excellence_requests.start_date',
						    	  'excellence_requests.end_date',
						    	  'excellence_requests.status'

	                              )
						    -> paginate(10);


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



		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'requests' => $providerRequests]);
	}

 

public function addExcellenceRequests(Request $request){
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم اضافه  العرض  بنجاح ',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
				3 => 'تاريخ  بدأ  ونهاية العرض لابد ان يكون علي الشكل  (yyyy-mm-dd H:i:s)' ,
				4 => 'صورة غير صالحة ',
				5 =>   'تاريح بدا العرض اكبر من تاريخ انتهاء العرض ',
				6 =>   'لابد ان يكون تاريج بدا العرض اكبر من او  يساوي تاريخ اليوم ' ,
				7 =>  'فشل في اضافه  العرض ',
				8 =>'التصنيف المختار غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Offer added successfully',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>  'start and end date  must be in format (yyyy-mm-dd H:i:s)',
				4 =>  'image not valid' ,
				5 => 'start date greater than end date ',
				6 =>  'start date of the offer must greater  than or equal to  today',
				7 =>'Failed to add Offers',
				8 =>'Category choosed not exists',

				 
			);
		}

		$messages = array(

			'required'                => 1,
			'access_token.required'   => 2,
			'date_format'             => 3,
			'mimes'                   => 4,
			'after'                   => 5,
			'after_or_equal'          => 6,
			'main_category_id.exists' => 8
		);

         $rules=[

			'access_token'         => 'required',
			'country_code'         => 'required',
			'main_category_id'     => 'required|exists:categories,cat_id',
			'phone'                => 'required',
			'name'                 => 'required', 
 			'start_date'           => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d'),
			'end_date'             => 'required|date_format:Y-m-d H:i:s|after:start_date'
    
		    ];


		$validator = Validator::make($request->all(),$rules, $messages);

 
		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

 
         $inputs = $request -> only('name','start_date','end_date','country_code','phone','main_category_id');

         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
             

              
 
           
			try {
 

				$id=DB::table('excellence_requests') -> insertGetId($inputs);
			         
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0], 'request_id' => $id]);

			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 7, 'msg' => $msg[7]]);
			}
		
	}



public function payExcellenceRequests(Request $request){

		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => 'تم   تغيير حاله الطلب  الي مدفوع ',
 				1 => 'توكن المستخدم غير موجود ',
 				2 => 'لابد من ادخال رقم  الطلب  ',
				3 =>  'كل الحقول مطلوبة ',
				4 =>  'فشل في   تغيير حالة  الطلب   ',
				5 => 'العرض غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Request  status changed successfully to Paid',
				1 => 'access_token required',
				2 => 'offer id  required',
				3 =>  'All fields required', 
				4 =>  'Failed to change status',
				5 =>  'Request  not exists',
				 
			);
		}

		$messages = array(
			 
			'access_token.required'      => 1,
			'request_id.required'        => 2,
			'required'                   => 3,
			'request_id.exists'          => 5

		);

		$validator = Validator::make($request->all(),[

			'access_token'           => 'required',
			'request_id'             => 'required|exists:excellence_requests,id' ,
			'paid_amount'            => 'required'
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
 
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // offer_id

              $exRequest = DB::table('excellence_requests') 
                                -> where ([
                                	         'provider_id'   =>   $inputs['provider_id'],
                                	         'id'            =>   $request -> request_id
                                         ]) ;


             if(!$exRequest -> first()){
 
                return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
             }


                
			try {
 
                   $exRequest -> update([
                           

                             'paid'          => '1',
                             'paid_amount'   => $request -> paid_amount,
                             'status'        => '0'


                   	    ]);     
 
				return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0]]);


			} catch (Exception $e) {
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}

 

public function ExcellenceRequestDetails(Request $request){

 
		$lang = $request->input('lang');
		 
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'كل الحقول مطلوبه',
 				2 => 'توكن المستخدم غير موجود ',
 				3 => 'لابد من ادخال رقم  طلب التميز   ',
				4 =>  '  رقم الطلب   غير موجود ',
			);


		}else{
			$msg = array(
				0 => '',
				1 => 'All fields are required',
				2 => 'access_token required',
				3 =>'request id  required',
				4 => 'request  not found ',
  
			);
		}

		$messages = array(
			 
			'access_token.required'    => 2,
			'required'                 => 1,
			'request_id.required'      => 3,
			'request_id.exists'        => 4
 
		);

		$validator = Validator::make($request->all(),[
			'access_token'         => 'required',
 			'request_id'           => 'required|exists:excellence_requests,id' 
			
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 

  
         
         $inputs['provider_id']  =  $this->get_id($request,'providers','provider_id');
              
                    // cat_id

      $providerRequest = DB::table('excellence_requests') 
                        -> where ([
                        	         'provider_id'           =>   $inputs['provider_id'],
                        	         'excellence_requests.id' =>   $request -> request_id

                                 ])
                        
                       -> select(
                       	           'excellence_requests.id As request_id',
                       	           'excellence_requests.status',
                       	            'paid',
							        'paid_amount',
                       	           DB::raw('DATE(excellence_requests.start_date) AS start_date'),
                       	           DB::raw('DATE(excellence_requests.end_date) AS end_date')
 
                                ) 

                       -> first();
 
                 $coastPerDay  = DB::table('app_settings') -> select('excellence_day_coast') -> first();
 
if($providerRequest){
 
		    if($providerRequest -> status == 0){


		    	$providerRequest -> order_date =  DATE('Y-m-d',strtotime($providerRequest ->start_date));
		     }else {

		    	$providerRequest -> expire_date =  DATE('Y-m-d',strtotime($providerRequest ->end_date));
		    }


                    $start_date = new DateTime($providerRequest  -> start_date);
					$end_date   = new DateTime($providerRequest -> end_date);

					$interval = $start_date -> diff($end_date);
				 	$days     = $interval   -> format('%a');
 
					 $providerRequest -> daysCount = $days;

                     if($coastPerDay -> 	excellence_day_coast > 0){

					   $providerRequest -> daysCount = (int) $days ;
					   $providerRequest -> totalCost =$days * $coastPerDay ->excellence_day_coast;
					}
					 else
					 	$providerRequest -> daysCount = 0;
			}
 
  
		return response()->json(['status'=> true, 'errNum' => 0, 'msg' => $msg[0],'data' => $providerRequest]);
 
}
  
  
	public function fetchOrdersCounts(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'المتجر غير موجود'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'provider no found'

			);
		}
 
		if(empty($request->input('access_token'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
 		
		 $provider_id = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		      }
 
		    
			
		//get new orders count
		$pendings    = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->where('status_id', 1)
										   ->count();


		$current = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->whereIn('status_id', [1,2])
										   ->count();


		$old = DB::table('orders_headers')->where('provider_id', $provider_id)
										   ->whereIn('status_id', [3,4])
										   ->count();
 
		return response()->json([
									'status' => true,
									'errNum' => 0,
									'msg'    => $msg[0],
									'pending_orders_count'     => $pendings,
									'current_orders_count' => $current,
 									'old_orders_count'     => $old,
 								]);
	}


	public function getProviderOrders(Request $request){
	    
 	  $lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون 1 او 2 او 3 او 4',
				4 => 'لا يوجد طلبات بعد',
				5 =>  'المتجر غير موجود ',
			);
			$payment_col  = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col	  = "order_status.ar_desc AS status_text";
		}else{
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'type is required',
				3 => 'type must be 1, 2, 3 ',
				4 => 'There is no ordes yet',
				5 => 'Provider not Found'
			);
			$payment_col  = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col	  = "order_status.en_desc AS status_text";
		}

		$messages  = array(
			'access_token.required' => 1,
			'type.required'         => 2,
			'in'                    => 3
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
			'type'         => 'required|in:1,2,3'
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		 $provider_id = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		      }
  


 			$type 		  = $request->input('type');
			$conditions[] = ['providers.provider_id','=', $provider_id];
			$inCondition = [];
			if($type == 1){
			   
				$inCondition = [1,2];
			  
			//  array_push($conditions, [DB::raw('orders_headers.created_at') , '>', Carbon::now()->addHours(1)->subMinutes($time_counter_in_min)]);
				 
			}elseif($type == 2){
				$inCondition = [3];
				//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
			}elseif($type == 3){
				$inCondition = [4];
				//array_push($conditions, ['orders_headers.status_id' , '!=', 1]);
				//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '>', $today]);
			} 
		 
			//get orders
 	$orders = \App\Order_header::where($conditions) 
 	                     ->whereIn('orders_headers.status_id', $inCondition)
 						->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
						->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
                        ->join('users', 'orders_headers.user_id', 'users.user_id')
						->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
						->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
						->select(
						            'orders_headers.order_id',
						            'orders_headers.order_code',
                                    'orders_headers.delivery_id',
                                    'providers.store_name AS store_name',
                                    'orders_headers.address',
                                    'users.full_name AS user_name',
                                    'orders_headers.total_value',
                                    $payment_col, 
                                    $delivery_col,
                                    DB::raw("(SELECT count(order_products.id) FROM order_products WHERE order_products.order_id = orders_headers.order_id) AS products_count"),
                                    $status_col,
                                    'orders_headers.status_id',
                                    DB::raw('DATE(orders_headers.created_at) AS created_date'),
                                    DB::raw('TIME(orders_headers.created_at) AS created_time')
                        )
                        
						->orderBy('orders_headers.order_id', 'DESC')
						->paginate(10);
 
			
			return response()->json([
										'status' 			    => true,
										'errNum' 			    => 0,
										'msg' 				    => $msg[0],
										'orders' 			    => $orders,
										 
									]);
		
	}


	public function orderAcceptance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العمليه بنجاح',
				1 => 'رقم الطلب مطلوب',
				2 => 'رقم مقدم الخدمه مطلوب',
				3 => 'نوع العمليه مطلوب',
				4 => 'نوع العمليه يجب ان يكو (accept or reject)',
				5 => 'فشلت العمليه من فضلك حاول فى وقت لاحق',
				6 => 'ليس لديك صلاحية لتعديل هذا الطلب',
				7 => 'لقد تم الغاء الطلب من قبل ',
				8 => 'لقد تم قبول العرض من قبل '
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'order_id is required',
				2 => 'access_token is required',
				3 => 'type is required',
				4 => 'type must be (accept or reject)',
				5 => 'Process failed please try again later',
				6 => 'you can not access this order',
				7 => 'Order Rejected Before!',
				8 => 'Order already Accepted before!',

			);
		}

		$messages = array(
			'order_id.required'     => 1,
			'access_token.required' => 2,
			'type.required'          => 3,
			'in' 				     => 4
		);

		$validator = Validator::make($request->all(), [
			'order_id'      => 'required',
			'access_token'  => 'required',
			'type'          => 'required|in:0,1',
		], $messages);


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		 $provider_id = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		      }
 


			$type = $request->input('type');
			if($type == "1"){
				if($lang == 'ar'){
					$push_notif_title   ='قبول الطلب';
					$push_notif_message = 'قام مقدم الخدمة بقبول طلبك';
				}else{
					$push_notif_title   ='Order accepted';
					$push_notif_message = 'The provider accepted your order';
				}
				$status = 2;
			}else{
				if($lang == 'ar'){
					$push_notif_title   ='رفض الطلب';
					$push_notif_message = 'قام مقدم الخدمه برفض طلبك  وتم استرجاع المبلغ برصيدك  '  .$request->input('order_id');
				}else{
					$push_notif_title   ='Order rejected';
					$push_notif_message = 'The provider rejected your order and money return to your balance' .$request->input('order_id');
				}
				$status = 4;
			}
			try {
 				$order_id    = $request->input('order_id');
				//get order
				$orderDetails = DB::table('orders_headers')->where('order_id', $order_id)->select(
				    'user_id', 'payment_type', 'total_value' , 'provider_id','status_id')->first();
                //return "provider_id : " . var_dump($provider_id) . " order_id " . var_dump($orderDetails->provider_id);
				if($provider_id != $orderDetails->provider_id){
                    return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
                }


               if(($orderDetails  -> status_id == 4 or $orderDetails  -> status_id == "4"  )&& $request -> type == 0){
                    return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
                }


                  if(($orderDetails  -> status_id == 2 or $orderDetails  -> status_id == "2" )&& $request -> type == 1 ){
                    return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
                }

 
				if($orderDetails){
					$payment_type = $orderDetails->payment_type;
					$total_value  = $orderDetails->total_value;
					$user_id      = $orderDetails->user_id;
				}else{
					return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
				}

				DB::transaction(function() use ($status, $order_id, $provider_id, $payment_type, $total_value, $user_id){

					DB::table("orders_headers")->where('order_id', $order_id)->update(['status_id' => $status]);

					date_default_timezone_set('Asia/Riyadh');

					DB::table("orders_headers")->where('order_id', $order_id)->update(['provider_accept_order_date' => date("Y/m/d H:i:s", time())]);

					DB::table("order_products")->where('order_id', $order_id)->update(['status' => $status]);
					

					  // if order reject and payment is VISA  
					if($status == 4 || $status == "4"){
						if($payment_type != 1 && $payment_type != "1"){
							 
 					            $balance = DB::table("balances")
					                ->where("actor_id", $order->user_id)
					                ->where("actor_type", "user")
					                ->first();

					            if($balance){
					                DB::table("balances")
					                    ->where("actor_id", $order->user_id)
					                    ->where("actor_type", "user")
					                    ->update([
					                        "balance" => $balance->balance + $order->total_value
					                    ]);
					            }
 
						}
					}
				});

				$notif_data = array();
				$notif_data['title']      = $push_notif_title;
			    $notif_data['message']    = $push_notif_message;
			    $notif_data['order_id']   = $order_id;
			    $notif_data['notif_type'] = 'order_acceptance';
			    
			    //device register for  firebase
			    $user_token = User::where('orders_headers.order_id', $order_id)
			    				  ->join('orders_headers', 'users.user_id', '=', 'orders_headers.user_id')
			    				  ->select('users.device_reg_id','users.user_id')
			    				  ->first();
			    				
			    	//send notification to mobile Firebase			  
			    if($user_token){
			    	$push_notif = (new Push())->send($user_token->device_reg_id,$notif_data,(new Push())->user_key);
			    }


			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $push_notif_title,
		                "ar_title"           => $push_notif_title,
		                "en_content"         => $push_notif_message,
		                "ar_content"         => $push_notif_message,
		                "notification_type"  => 1,
		                "actor_id"           => $user_token->user_id,
		                "actor_type"         => "user",
		                "action_id"          => $order_id

		            ]);


				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		
	}

	 
 

	public function changeOrderStatus(Request $request){
		 
        $payment = "";
        $net = "";
        $app_value = "";
        $delivery_app_value = "";
        $totalVal = "";
        $userId = "";

		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العمليه بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'فشلت العمليه من فضلك حاول لاحقا',
				3 => 'رقم الحالة يجب ان يكون 2 او 3 او 4 او 5',
				4 => 'رقم الموصل مطلوب إذا كان رقم الحاله = 3 و طريقة التوصيل = 1',
				5 => 'رقم الموصل خطأ',
				6 => 'التاجر  غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'All fields are required',
				2 => 'Process failed, please try again later',
				3 => 'status_id must be 2,3,4 or 5',
				4 => 'delivery id is required if status id = 3 AND delivery_method = 1',
				5 => 'Invalid delivery_id',
				6 => 'Provider not exists',
			);
		}

		$messages = array(
			'required'    => 1,
			'in'          => 3
		);

		$validator = Validator::make($request->all(), [
			'order_id'         => 'required',
			'access_token'     => 'required',
			'status_id'        => 'required|in:2,3,4'
		], $messages);

		if($validator->fails()){


			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		$order_id        = $request->input('order_id');
		$status          = $request->input('status_id');
		 $provider_id    = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		      }
		 

		$get = DB::table('orders_headers')
            ->where('order_id', $order_id)
            ->select('delivery_method', 'delivery_price', 'delivery_app_value')
            ->first();
            
		$delivery_method         = $get->delivery_method;
 		$delivery_price          = $get->delivery_price;

		 



 		//get order payment method  if order deliveried 
		if($status == 3){
			$orderCredits = DB::table('orders_headers')
									->where("order_id", $order_id)
									->select("payment_type",'net_value', 'app_percentage', 'app_value', 'delivery_app_value', 'user_id', 'total_value')
									->first();
			if($orderCredits){
				$payment   = $orderCredits->payment_type;
				$net       = $orderCredits->net_value;
				$app_value = $orderCredits->app_value;
				$delivery_app_value = $orderCredits->delivery_app_value;
				$totalVal  = $orderCredits->total_value;
				$userId    = $orderCredits->user_id;
			}else{
				$payment   = "";
				$net 	   = 0;
				$app_value = 0;
				$totalVal  = 0;
				$userId    = 0;
				$delivery_app_value = 0;
			}
		}else{
			$payment   = "";
			$net 	   = 0;
			$app_value = 0;
			$delivery_app_value = 0;
		}



		try {
 
				$updates =[];
				$updates['status_id'] =  $status;
     	  						   
				DB::table('order_products')->where('order_id', $order_id)
				  						  ->update(['status' => $status]);

				  						  //get orderDetails and delivery, user reg id
		  $order_data = DB::table('orders_headers')->where('orders_headers.order_id', $order_id)
				   				 ->join('users', 'orders_headers.user_id', '=', 'users.user_id')
				   				 ->select('orders_headers.order_id', 'orders_headers.user_id','orders_headers.order_code','orders_headers.address AS user_address','orders_headers.user_longitude',
				   	        			  'orders_headers.user_latitude', 'users.device_reg_id','orders_headers.total_value')
				   				 ->first();


				if($status == 4 or $status == "4"){
					 	 
					 if($payment != 1){
                                

                       // money return to user balance if order rejected and payment by visa 
						     
			                 $userBalance = DB::table("balances")
			                ->where("actor_id", $order_data->user_id)
			                ->where("type", "user")
			                ->first();

			                  if($userBalance){
				                DB::table("balances")
				                    ->where("actor_id", $order_data ->user_id)
				                    ->where("type", "user")
				                    ->update([
				                        "current_balance" => $userBalance->current_balance + $order_data ->total_value
				                    ]);
				               }
 
					 }	else{    //payment COD
                            
                         //there is no change on any balances  to user or provider

					 }
  
				}


				if($status == 3  or $status == "3"){


					if($payment == 2 or $payment == "2" ){   // by visa 


 
						DB::table("balances")->where("actor_id", $provider_id)
											 ->where('type', 'provider')
											 ->update([ 'current_balance' => DB::raw('current_balance + '. $net) ]);

						//user balance not affect because there is no paid by  current balance 					 
                            
					    /*if($delivery_method == 1 || $delivery_method == "1"){   // COD
							DB::table('balances')->where('actor_id', $delivery_id)
												 ->where('type', 'delivery')
												 ->update(['current_balance' => DB::raw('current_balance + '. $new_delivery_price)]);
						}*/
 
				}else{   //COD payment
                               
                            
                             DB::table("balances")->where("actor_id", $provider_id)
											 ->where('type', 'provider')
											 ->update([ 'current_balance' => DB::raw('current_balance - '.  $app_value) ]);

					  }

             }
			

				 DB::table('orders_headers') ->where('orders_headers.order_id', $order_id) -> update($updates);

				if($lang == "ar"){
					$userTitle   = "تم تعديل حالة طلبك" .$order_id;
					$userMessage = "تم تعديل حالة طلبك";
				}else{
					$userTitle   = "Your order status has been updated" .$order_id;
					$userMessage = "Your order status has been changed";
				}
				//send to user
				$notif_data = array();
				$notif_data['title']      = $userTitle;
			    $notif_data['message']    = $userMessage;
			    $notif_data['order_id']   = $order_data->order_id;
			    $notif_data['notif_type'] = 'order';


							
			    	//send notification to mobile Firebase			  
			    if($order_data){
			    	$push_notif = (new Push())->send($order_data->device_reg_id,$notif_data,(new Push())->user_key);
			    }


			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $userTitle,
		                "ar_title"           => $userTitle,
		                "en_content"         => $userMessage,
		                "ar_content"         => $userMessage,
		                "notification_type"  => 1,
		                "actor_id"           => $order_data->user_id,
		                "actor_type"         => "user",
		                "action_id"          => $order_id

		            ]);


			/*	if($status == 3 && $delivery_method == 1){
					if($lang == "ar"){
						$deliveryTitle   = "طلب جديد";
						$deliveryMessage = "تم إرسال طلب جديد إليك";
					}else{
						$deliveryTitle   = "New order";
						$deliveryMessage = "A new order has been send for you";
					}

					if(!empty($delivery_id)){
						$deliveryToken= DB::table('deliveries')->where('delivery_id', $delivery_id)
										   				   ->select('device_reg_id AS delivery_token')
										   				   ->first()->delivery_token;
					}else{
						$deliveryToken = "";
					}
					//send to delivery
					$notif_data = array();
					$notif_data['title']   = $deliveryTitle;
				    $notif_data['message'] = $deliveryMessage;
				    $notif_data['order_id'] 	  = $order_data->order_id;
				    $notif_data['user_longitude'] = $order_data->user_longitude;
				    $notif_data['user_latitude']  = $order_data->user_latitude;
				    $notif_data['user_address']	  = $order_data->user_address;
				    $notif_data['notif_type']	  = 'order';
					$push_notif = $this->singleSend($deliveryToken, $notif_data, $this->delivery_key);
				}*/

			
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}
	}


      public  function getInvitationCode(Request  $request){
           
            $lang = $request->input('lang');
			if($lang == "ar"){
				$msg = array(
					0 => '',
					1 => 'رقم مقدم الخدمه مطلوب',
					2 => 'المتجر غير موجود',
					3 => 'تم جلب البيانات '
				);
			}else{
				$msg = array(
					0 => '',
					1 => 'access_token is required',
					2 => 'provider not exists',
					3 => 'data retrieved successfully'


				);
			}

			$messages = array(
				'required' => 1
			);

			$validator = Validator::make($request->all(), [
				'access_token' => 'required'
			], $messages);

			if($validator->fails()){
				$error = $validator->errors()->first();
				return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
			}

			 $provider_id    = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		      }
		 
 
		      if($check  -> invitationCode){

		      	      return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3] , 'invitationCode' => $check  -> invitationCode]);
		      }
                

                  
                  $randCode = $this->getRandomString(9);

                  DB::table('providers')   -> where('provider_id',$provider_id) ->update(['invitationCode' => $randCode]);


		        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3] , 'invitationCode' => $randCode]);
              
      }

		public function getProviderBalance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'المتجر غير موجود',
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'provider not exists'


			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

            // check if the user has bank data
              $get_provider_bank = DB::table("withdraw_balance")
                ->select("*")
                ->where("actor_id" , $request->input("provider_id"))
                ->where("type" , "provider")
                ->get();
               

              
               $provider_id    = $this->get_id($request,'providers','provider_id');

		        if($provider_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		        }

		      $check = DB::table('providers')   -> where('provider_id',$provider_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		      }
		 



            //get balaces
            $balance = DB::table('balances')
                ->where('actor_id',  $provider_id)
                ->where('type', 'provider')
                ->select('current_balance', 'due_balance', 'updated_at' , 'forbidden_balance')
                ->first();

 
            if($balance){
                $current_balance = $balance->current_balance;
                $due_balance     = $balance->due_balance;
                $forbidden       = $balance->forbidden_balance;
                $updated         = $balance->updated_at;
            }else{
                $current_balance = "";
                $due_balance     = "";
                $forbidden       = "";
                $updated         = "";
            }


            
            if(isset($get_provider_bank) && $get_provider_bank -> count() > 0){
                //return empty($get_provider_bank);
                
                $last_entry = $get_provider_bank[count($get_provider_bank) -1 ];
                $bank_name = $last_entry->bank_name;
                $bank_account = $last_entry->account_num;
                $bank_username = $last_entry->name;
                $bank_phone = $last_entry->phone;
            }else{

                $bank_name = "";
                $bank_account = "";
                $bank_username = "";
                $bank_phone = "";
            }



            $financialTransactions = DB::table('withdraw_balance') 
             -> join('providers', "providers.provider_id" , "withdraw_balance.actor_id") 
            -> where('withdraw_balance.type','providers')
            -> select(
                                "withdraw_balance.*",
                                "providers.full_name AS name"
                                
                    )
            ->get();





            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0],'balance' => ["current_balance"=>$current_balance , "due_balance" => $due_balance , "forbidden_balance" => $forbidden  , "updated_at" => $updated] , 'bank_name'=>$bank_name , 'bank_username'=>$bank_username , 'bank_phone'=>$bank_phone , 'account_num' =>$bank_account,'financialTransactions' => $financialTransactions]);

             
 

		
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
				5 => 'المستهدم غير موجود ',
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
			 	$notificationSettings = DB::table('notification_settings') -> where('type',$actor)  -> where('actor_id',$actor_id) ->first();
				 

				  if($notificationSettings){
                         unset($notificationSettings -> id);//
                       return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[6],'settings' =>  $notificationSettings]);   
				  }
			
             return response()->json(['status' => true, 'errNum' => 7, 'msg' => $msg[7] ,'settings' => []]);                      
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
				11 => 'تم ا حفظ البيانات بنجاح ',

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
  
			    $inputs = $request -> only('new_order','cancelled_order','offer_request','admin_notify','ticket_notify','order_delay');

			    if($set){

                    $settings -> update($inputs);

                     return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[10]]);


			    }else{
                     
                 $inputs = $request -> only('new_order','cancelled_order','offer_request','admin_notify','ticket_notify','order_delay');

                 $inputs['actor_id'] = $actor_id;
                 $inputs['type']     = $actor;


               $settings = DB::table('notification_settings') 
                                     -> insert($inputs);

                return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[11]]);

			    }

          
	}


		public function withdraw(Request$request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'رقم صاحب الطلب مطلوب',
				2 => 'الرصيد الحالى مطلوب',
				3 => 'الرصيد المستحق مطلوب',
				4 => 'فشلت العملية من فضلك حاول لاحقا',
				5 => 'لديك طلبات لم يتم الرد عليها بعد',
				6 => 'ادخل رقم الرصيد المستحق المراد سحبة',
				7 => 'current_balance يجب ان يكون رقم',
				8 => 'رقم الرصيد الحالى مطلوب',
				9 => 'الاسم مطلوب',
				10 => 'رقم الحساب مطلوب',
				11 => 'رقم الهاتف مطلوب',
				12 => 'ليس لديك رصيد كافى لاتمام هذة العملية',
				13 => 'رصيدك الحالى اقل من الحد الادنى لسحب الرصيد',
                14 => "النوع يجب ان يكون اما مقدم او مسوق",
                15 => "النوع مطلوب",
                16 => "forbidden_balance  مطلوب ",
                17 => "forbidden_balance لابد ان يكون ارقام ",
                18 => "النوع لابد ان يكون  providers or deliveries ",
                19 => "المستخدم غير موجود ",


			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'actor_id is required',
				2 => 'current_balance is required',
				3 => 'due_balance is required',
				4 => 'Process failed, please try again later',
				5 => 'You already have pending requests',
				6 => 'Enter a valid current_balance number',
				7 => 'current_balance must be a number',
				8 => 'bank_name is required',
				9 => 'name is required',
				10 => 'account_num is required',
				11 => 'phone is required',
				12 => "You Don't have enough balance",
				13 => "Your balance is less than minimum balance to withdraw",
				14 => "type must be either provider or marketer",
				15 => "type is required",
				16 => "forbidden_balance is required",
				17 => 'forbidden_balance must be a number',
				18 => 'type must be only providers or deliveries ',
				19 => "actor not found"

			);
		}

		$messages = array(
			'access_token.required'    => 1,
			'current_balance.required' => 2,
			'type.required'            => 15,
			'current_balance.numeric'  => 7,
			'due_balance.required'     => 3,
			'bank_name.required'       => 8,
			'name.required'            => 9,
			'account_num.required'     => 10,
			'phone.required'           => 11,
			'forbidden_balance.required'       => 16,
			'forbidden_balance.numeric'        => 17,
			'type.in'                          => 18,

		);

		$validator = Validator::make($request->all(), [
			'access_token'    => 'required',
			'type'            => 'required|in:providers,deliveries',
            'bank_name'       => 'required',
            'name'            => 'required',
            'account_num'     => 'required',
            'phone' 	      => 'required',

		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		    $type = "";
		    if($request->input("type") == "providers"){
		        $type  = "providers";
		        $colum = "provider_id";
            }elseif($request->input("type") == "deliveries"){
		        $type = "deliveries";
		        $colum = "delivery_id";
            }else{
                return response()->json(['status' => false  , 'msg' => $msg[14]]);
            }


               
              $actor_id    = $this->get_id($request,$type,$colum);

		        if($actor_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 19, 'msg' => $msg[19]]);
		        }

		      $check = DB::table($type)   -> where($colum,$actor_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 19, 'msg' => $msg[19]]);
		      }


            // insert bank account data into database
            $actor_bank_data = DB::table("withdraw_balance")
                ->where("actor_id" ,$actor_id)
                ->where("type" , $type)
                ->first();
		  
            
			//check if there is pending requests
			$check  = DB::table('withdraw_balance')->where('actor_id', $actor_id)
												   ->where('type', $type)
												   ->where('status', 1)
					
												   ->first();

			   // cannot withdraw when has pending request 
			if($check){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

            if($type =="providers"){
                  
                  $balType ="provider";

            }else{

                    $balType ="delivery";            	
            }

 
            // check if the user requested blance is avaliable
            $provider_balace = DB::table("balances")
                                ->select("current_balance","due_balance","forbidden_balance")
                                ->where("actor_id" ,$actor_id)
                                ->where("type" , $balType)
                                ->first();

            $provider_current_balance = $provider_balace->current_balance;
 


            //check if the current balance is greater than min limit of withdrawing
            $min_balance = DB::table("app_settings")
                            ->select("min_balace_to_withdraw")
                            ->first();
            if($provider_balace -> current_balance < $min_balance->min_balace_to_withdraw){
                return response()->json(['status' => false, 'errNum' => 13, 'msg' => $msg[13]]);
            }


			$insert = DB::table("withdraw_balance")->insert([
						 'actor_id'        => $actor_id,
						 'current_balance' => $provider_balace -> current_balance ,
						 'due_balance'     => $provider_balace -> due_balance ,
                         'forbidden'       => $provider_balace ->forbidden_balance,
						 'type' 		   => $type,
                         'status'          =>  0,
						 'bank_name' 	   => $request->input('bank_name'),
						 'name' 		   => $request->input('name'),
						 'account_num' 	   => $request->input('account_num'),
						 'phone' 		   => $request->input('phone')

					  ]);
			if($insert){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		
	}


public function prepareSearch(Request $request)
{
   $lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات'
			);
 			$cat_col	  = "order_status.ar_desc AS status_name";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data'
			);
 			$cat_col 	  = "order_status.en_desc AS status_name";
		}

 	 	$order_status = DB::table('order_status')
  		-> select('order_status.status_id',
 		 $cat_col
 		)->get();

		return response()->json(['status'=>true, 'errNum' => 0, 'msg' => $msg[0], 'order_status' => $order_status]);

}

public function Providersearch(Request $request){
	      
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد نتائج لبحثك',
				2 => 'يجب ان تختار التاريخ عند البحث بالوقت',
				3 => 'يجب إرسال تفاصيل موقع المستخدم',
				4 => 'المسافه يجب ان تكون بالأرقام',
				5 => 'الوقت يجب ان يكون فى تنسيق h:i (09:05)',
				6 => 'التاريخ يجب ان يكون فى تنسيق yyyy-mm-dd',
				7 => 'يجب إختيار المدينة المراد البحث بها',
				8 => 'type يجب ان يكون provider او meal',
				9 => 'access_token required',
				10 => 'التاجر غير موجود '

			);

			$status_col ="ar_desc";
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no result for your search',
				2 => 'You must determine the date if you search with time',
				3 => 'user longitude and latitude is required',
				4 => 'distance must be in number',
				5 => 'time must be in format H:i ex:- (09:05)',
				6 => 'date must be in format yyyy-mm-dd',
				7 => 'Please select city',
				8 => 'type attribute must be provider or meal',
				9 => 'access_token required' ,
				10 => 'Provider not exists'

			);
			$status_col ="en_desc";
		}

  	$messages = array(
			'access_token.required'    => 9,
		);


		$validator = Validator::make($request->all(), [
			'access_token'    => 'required',
		 

		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

             $type ="providers";
             $colum ="provider_id";

              $actor_id    = $this->get_id($request,$type,$colum);

		        if($actor_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		        }

		      $check = DB::table($type)   -> where($colum,$actor_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		      }




		$user_name     	     = $request->input('user_name');
 		$status_id         	 = $request->input('status_id');
 		$order_id            = $request->input('order_id'); 

            //(SELECT users.full_name FROM users WHERE orders_headers.user_id = users.user_id) AS user_name
 	   	$virtualTble = "SELECT orders_headers.order_id , orders_headers.order_code, orders_headers.total_value,orders_headers.status_id ,orders_headers.user_id ,(SELECT order_status.{$status_col} FROM order_status WHERE orders_headers.status_id = order_status.status_id) AS status_text,(SELECT users.full_name FROM users WHERE orders_headers.user_id = users.user_id) AS user_name
 	   	 FROM `orders_headers` WHERE orders_headers.provider_id ={$actor_id}"
 	   	 ;  

		  

		  $conditions=[];

		  if(!empty($user_name) && $user_name !== 0 && $user_name !== 0.0 && $user_name !== "0.0"){
			array_push($conditions, ['tble.user_name', 'like', '%'.$user_name.'%']);
		}  

		if(!empty($status_id) && $status_id !== 0 && $status_id !== "0" && $status_id !== 0.0 && $status_id !== "0.0"){
			array_push($conditions, ['tble.status_id', '=', $status_id]);
		}


      if(!empty($order_id) && $order_id !== 0 && $order_id !== "0" && $order_id !== 0.0 && $order_id !== "0.0"){
			array_push($conditions, ['tble.order_id', '=', $order_id]);
		}
		   
		if(!empty($conditions)){
		 	$result = DB::table(DB::raw("(".$virtualTble.") AS tble"))
						->select('tble.order_id', 'tble.order_code', 'tble.total_value','tble.status_id','tble.status_text','tble.user_name')
						->where($conditions)->get();

		}else{
			$result = DB::table(DB::raw("(".$virtualTble.") AS tble"))
						->select('tble.order_id', 'tble.order_code', 'tble.total_value','tble.status_id','tble.status_text','tble.user_name')
						->get();
		}
 
		
 		if(isset($result) && $result->count() > 0){
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'result' => $result]);
		}else{
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}




	 
}