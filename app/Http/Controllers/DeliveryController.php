<?php

namespace App\Http\Controllers;
//********
/**
 * Class DeliveryController.
 * it is a class to manage all delivery functionalities
  
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
use App\Meals;
use App\Deliveries;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Carbon\Carbon;
use DateTime;
use App\Http\Controllers\PushNotificationController as Push;

class DeliveryController extends Controller
{
	 
	public function __construct(Request $request){
		 
	}

	//method to prevent visiting any api link
	public function echoEmpty(){

		echo "";
	}
 
 
	protected function getCountries($lang, $selected = NULL){
		if($lang == "ar"){
			$country_col = "country_ar_name AS country_name";
		}else{
			$country_col = "country_en_name AS country_name";
		}

		if($selected != NULL){
			return DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = '.$selected.', 1, 0) AS chosen'), 'country_code')->get();
		}

		return DB::table('country')->select('country_id', $country_col, 'country_code')->get();
	}

	protected function getCities($lang, $selected = NULL){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		if($selected != NULL){
			return DB::table('city')
					 ->join('country', 'city.country_id', '=', 'country.country_id')
					 ->select('city.city_id', $city_col, DB::raw('IF(city.city_id = '.$selected.', 1, 0) AS chosen'), 'country.country_code')->get();
		}

		return DB::table('city')
				  ->join('country', 'city.country_id', '=', 'country.country_id')
				  ->select('city.city_id', $city_col, 'country_code')->get();
	}

	protected function getDeliveryData($id, $lang, $action = "get", $password = NULL, $phone = NULL){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		if($action == "get"){

                return Deliveries::where('deliveries.delivery_id', $id)
                    ->join('city', 'deliveries.city_id', 'city.city_id')
                    ->select('deliveries.delivery_id', 'deliveries.full_name AS delivery_name','deliveries.receive_orders' ,   'deliveries.publish','account_activated', 'deliveries.phone', 'deliveries.country_code','deliveries.device_reg_id',
                        'deliveries.longitude', 'deliveries.latitude','deliveries.car_number','deliveries.country_id', 'deliveries.delivery_rate','deliveries.city_id',$city_col,'token',
                        DB::raw('DATE(deliveries.created_at) AS created'),
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.license_img) AS license_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.car_form_img) AS car_form_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.Insurance_img) AS Insurance_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public\deliveryImages/',deliveries.authorization_img) AS authorization_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.national_img) AS national_img")
                         )
                    ->first();

		}elseif($action == "login"){
			return Deliveries::where('deliveries.password', md5($password))
					         ->where(function($q) use ($phone){
						         $q->where('deliveries.phone', $phone)
						           ->orWhere(DB::raw('CONCAT(deliveries.country_code,deliveries.phone)'), $phone);
						     })
					         ->join('city', 'deliveries.city_id', 'city.city_id')
					         ->select('deliveries.delivery_id', 'deliveries.full_name AS delivery_name','deliveries.receive_orders' ,   'deliveries.publish','account_activated', 'deliveries.phone', 'deliveries.country_code','deliveries.device_reg_id',
                        'deliveries.longitude', 'deliveries.latitude','deliveries.car_number','deliveries.country_id', 'deliveries.delivery_rate','deliveries.city_id',$city_col,'token',
                        DB::raw('DATE(deliveries.created_at) AS created'),
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.license_img) AS license_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.car_form_img) AS car_form_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.Insurance_img) AS Insurance_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public\deliveryImages/',deliveries.authorization_img) AS authorization_img"), 
                         DB::raw("CONCAT('".env('APP_URL')."','/public/deliveryImages/',deliveries.national_img) AS national_img")
                     )
					         ->first();
		}else{
			return NULL;
		}
		
	}

	public function getCountryCities($lang, $country){
		if($lang == "ar"){
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$city_col = "city.city_en_name AS city_name";
		}

		return DB::table('city')->where('city.country_id', $country)
								->join('country', 'city.country_id', '=', 'country.country_id')
								->select('city.city_id', $city_col, 'country.country_code')->get();
	}

	public function prepareSignUp(Request $request){
		$lang = $request->input('lang');
		 
        $countries = $this->getCountries($lang);
 
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'countries' => $countries]);
	}

	
	protected function saveImage($data, $image_ext, $path){
		if(!empty($data)){
			 
			$data = str_replace('\n', "", $data);
			$data = base64_decode($data);
			$im   = imagecreatefromstring($data);
			if ($im !== false) {
				$name = 'img-'.str_random(4).'.'.$image_ext;
				if ($image_ext == "png"){
					imagepng($im, $path . $name, 9);
				}else{
					imagejpeg($im, $path . $name, 100);
				}

				return $name;
			} else {
				return "";
			}
		}else{
			return "";
		}
	}
      

	 

	public function signUp(Request $request){
	    
	  //  return response() -> json($request);
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم التسجيل بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'كلا من المدينه و الدوله يجب ان تكون ارقاما',
				3 => 'صيغة البريد الإلكترونى غير صحيحة',
				4 => 'الرقم السرى لا يجب ان يقل عن 8 حروف',
				5 => 'فشل فى رفع الصور',
				6 => 'فشل التسجيل، من فضلك حاول فى وقت لاحق',
				7 => 'البريد الإلكترونى مستخدم من قبل',
				8 => 'رقم الجوال مستخدم من قبل',
				9 => 'جميع الصوره المطلوبه يجب ان تكون فى صيغة jpeg او png',
			    10 => 'التصنيفات مطلوبه',
			    11 =>  'الدولة غير موجوده ',
				12 => 'المدينة غير موجوده',
				13 => ' صيغه الهاتف غير  صحيحه لابد انت تبدا ب 5 , 05',
				14 => 'كلمتي المرور غير متطابقتان ',
				15 => 'license_img_ext is required',
				16 => 'car_form_ext is required',
				17 => 'Insurance_img_ext is required',
				18 => 'authorization_img_ext is required',
				19 => 'national_img_ext is required',
			);
		}else{
			$msg = array(
				0 => 'Signed up successfully',
				1 => 'All fields are required',
				2 => 'country and city must be numeric',
				3 => 'E-mail must be in email format',
				4 => 'Password can not be less than 8 characters',
				5 => 'Failed to upload images',
				6 => 'Failed to sign up, please try again later',
				7 => 'Repeated email',
				8 => 'Repeated phone',
				9 => 'Requested images must be jpeg or png type',
				10 => 'Categories are required',
				11 =>  'Country not exists',
				12 => 'City not exists',
				13 => 'phone format invalid must start with 5 or 05',
				14 => 'password not confirmed',
				15 => 'license_img_ext is required',
				16 => 'car_form_ext is required',
				17 => 'Insurance_img_ext is required',
				18 => 'authorization_img_ext is required',
				19 => 'national_img_ext is required',
				

			);
		}

		$messages = array(

			'required'                           => 1,
			'numeric'                            => 2,
			'email'                              => 3,
			'min'                                => 4,
 			'phone.unique'                       => 8,
			'mimes' 		                     => 9,
			'country_id.exists'                  => 11,
			'city_id.exists'                     => 12,
			'phone.regex'                        => 13,
			'password.confirmed'                 => 14,
			'license_img_ext.required_with'      => 15,
			'car_form_ext.required_with'         => 16,
			'Insurance_img_ext'                  => 17,
			'authorization_img_ext'              => 18,
			'national_img_ext'                   => 19,
 

		);

		$validator = Validator::make($request->all(), [
			'full_name'    		=> 'required',
			'country_id'   		=> 'required|exists:country,country_id',
			'city_id'    		=> 'required|exists:city,city_id',
			'phone'     		=> array('required','unique:deliveries,phone','regex:/^(05|5)([0-9]{8})$/'),

			'country_code'   		=> 'required',
			'car_number'      		=> 'required',
 			'longitude'    		    => 'required',
			'latitude'     		    => 'required',
			'device_reg_id'  	          	=> 'required',
			'password_confirmation'    		=> 'required',
			'password'       		=> 'required|min:8|confirmed',
			'license_img'       	=> 'required',
			'license_img_ext'       => 'required_with:license_img',
			'car_form_img'       	=> 'required',
			'car_form_ext'          => 'required_with:car_form_img',
			'Insurance_img'       	=> 'required',
			'Insurance_img_ext'     => 'required_with:Insurance_img',
			'authorization_img'     => 'required',
			'authorization_img_ext' => 'required_with:authorization_img',
			'national_img'       	=> 'required',
			'national_img_ext'      => 'required_with:national_img',

		], $messages);

		if($validator->fails()){

			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
	 
		}



 $data=[];

  $data = $request -> only('full_name','country_id','city_id','phone','car_number','latitude','longitude','device_reg_id');


		$license_img= "";
  if($request->input('license_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> license_img, $request->input('license_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $data['license_img']= $image;
    					}
 
            }


            $car_form_img= "";
  if($request->input('car_form_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> car_form_img, $request->input('car_form_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $data['car_form_img']= $image;
    					}
 
            }

$Insurance_img= "";
  if($request->input('Insurance_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> Insurance_img, $request->input('Insurance_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $data['Insurance_img']= $image;
    					}
 
            }

            $authorization_img= "";
  if($request->input('authorization_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> authorization_img, $request->input('authorization_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $data['authorization_img']= $image;
    					}
 
            }


             $national_img= "";
  if($request->input('national_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> national_img, $request->input('national_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $data['national_img']= $image;
    					}
 
            }

           

 
  $data['password']        = md5($request->input('password'));
  $data['country_code']    = $this -> checkCountryCodeFormate($request->input('country_code'));
  

   // send activation code to provider 
	    $code                   = $this->generate_random_number(4);
        $data['token']          = $this -> getRandomString(128);

        $data['activation_code'] = json_encode([
            'code'   => $code,
            'expiry' => Carbon::now()->addDays(1)->timestamp,
        ]);
        
        $message = (App()->getLocale() == "en")?
                    "Your Activation Code is :- " . $code :
                     "رقم الدخول الخاص بك هو :- " .$code ;
 
		 

			try {
				$id = 0;
				DB::transaction(function() use ($data, &$id){
					$id = DB::table('deliveries')->insertGetId($data);					
					 
					  if(!$id)  
							return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
						  
						 
					DB::table('balances')->insert(['actor_id' => $id, 'current_balance' => 0, 'due_balance' => 0, 'type' => 'delivery']);
				});
				
				$delivery = $this->getDeliveryData($id, $lang, "get");
				 
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'delivery' => $delivery]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
		
	}

public function checkCountryCodeFormate($str){
		     
		    	   if(mb_substr(trim($str), 0, 1) === '+'){
	                          return  $str;
	                  }
	                  
	                  return '+'.$str;	                  
		}


	public function activateDelivery(Request $request){
		 $lang = $request->input('lang');

          if($lang == "ar"){
			$msg = array(
				0 => 'تم التفعيل ',
				1 => 'كود غير صحيح ',
				2 => 'لابد من  ادخال الكود ',
				3 =>  'لابد من توكن المستخدم ',
				4 =>  'فشل التفعيل من فضلك حاول لاقحا',
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
  

         $delivery_id = $this->get_id($request,'deliveries','delivery_id');
         if($delivery_id ==0 ){

         	   return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
         }

         $delivery = Deliveries::where('delivery_id',$delivery_id);

        $activate_phone_hash = $delivery -> first() -> activation_code;
		$code                = json_decode($activate_phone_hash) -> code;

		 if($code  != $request -> code)
		  {
             return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		  }
  
        $data['account_activated']           = 1;
        $data['status']                      = 1;
        $data['activation_code']             = null;

        $delivery -> update($data);
 
        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);


		 
	}



	 public function resendActivationCode(Request $request){
 
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

     $delivery_id = $this->get_id($request,'deliveries','delivery_id');
     if($delivery_id == 0){

     	 return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
     }

    $delivery = Deliveries::where('delivery_id',$delivery_id )  ;
 
    $code          = $this->generate_random_number(4);
 
    $data['activation_code'] = json_encode([
        'code'   => $code,
        'expiry' => Carbon::now()->addDays(1)->timestamp,
    ]);
 

     $delivery -> update($data);

    $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                 "رقم الدخول الخاص بك هو :- " .$code ;
  
    $res = (new SmsController())->send($message , $delivery -> first() ->phone);
   

    return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

}



	  
	public function deliveryLogin(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله', 
				3 => 'كلمة السر مطلوبه', 
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل الحساب بعد',
				6 => 'فى إنتظار تفعيل الإدارة',
				7 => 'رقم الجهاز مطلوب'
			);
			$city_col = "city.city_ar_name AS city_name";
		}else{
			$msg = array(
				0 => 'Logined successfully',
				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'Password is required',
				4 => 'Wrong data',
				5 => 'You need to activate your account',
				6 => 'Waitting for management activation',
				7 => 'dev_reg_id is required'
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(
				'phone.required'        => 1,
				'password.required'     => 3,
				'dev_reg_id.required' 	=> 7

			);
		$validator = Validator::make($request->all(), [
			'phone'          => 'required',
			'password'       => 'required',
			'dev_reg_id'     => 'required'
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status'=> false, 'errNum' => $error, 'msg' => $msg[$error]]); 
		}

			$getDelivery = $this->getDeliveryData(0, $lang, "login", $request->input('password'), $request->input('phone'));

			if($getDelivery != NULL && !empty($getDelivery) && $getDelivery->count()){

				Deliveries::where('delivery_id', $getDelivery->delivery_id)->update(['device_reg_id' => $request->input('device_reg_id')]);
				if($getDelivery-> 	account_activated == 0 || $getDelivery-> 	account_activated == "0"){
					return response()->json(['status'=> false, 'errNum' => 5, 'delivery' => $getDelivery, 'msg' => $msg[5]]);
				}elseif($getDelivery-> publish == 0 || $getDelivery->publish == "0"){
				    return response()->json(['status'=> false, 'errNum' => 6, 'delivery' => $getDelivery, 'msg' => $msg[6]]);
				}
				
				return response()->json(['status'=> true, 'errNum' => 0, 'delivery' => $getDelivery, 'msg' => $msg[0]]); 
			}else{
				return response()->json(['status'=> false, 'errNum' => 4, 'msg' => $msg[4]]); 
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
				6 => 'ؤ'
				
			);
			 
		}else{
			$msg = array(
 				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'phone doesn\'t exists',
				4 => 'activation code sent successfully',
  				5 => 'phone not active'    ,
			);
			 
		}
	        $rules    = [
                   "phone" => "required|numeric|exists:deliveries,phone"
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

		        //select proser vider base on his/her phone number if exists
		        $DeliveryData = DB::table("deliveries")->where("phone" , $request->input("phone"))->select("delivery_id")->first();
 

		        $delivery = Deliveries::where('delivery_id',$DeliveryData -> delivery_id);
                 

 		        if($delivery -> first()->  account_activated == '0' or  $delivery -> first()->  account_activated == 0){

		            return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);

		        }


		        $code = $this -> generate_random_number(4);

		        $message = (App()->getLocale() == "en")?
		            "Your Activation Code is :- " . $code :
		            $code . "رقم الدخول الخاص بك هو :- " ;

		        $activation_code = json_encode([
		            'code'   => $code,
		            'expiry' => Carbon::now()->addDays(1)->timestamp,
		        ]);

		        $delivery -> update([
		        	 'activation_code'   => $activation_code,
		        ]);

		        (new SmsController())->send($message , $delivery ->first()->phone);

		        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4] , "access_token" => $delivery -> first() ->token]);

	}

	 

	   public function updatePassword(Request $request){

           $lang = $request->input('lang');

        $rules      = [
            "password"      => "required|min:8|confirmed",
            "access_token"  => "required"
        ];

        $messages   = [
            "password.required"     => 1,
            "password.required"     => 1,
            'password.min'          => 2,
            'password.confirmed'    => 3,
            'access_token.required' => 5
        ];



		if($lang == "ar"){
			$msg = array(
 				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور  8 احرف ع الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
 				4 => 'تم تغيير كلمة  المرور بنجاح ',
 				5 => 'توكن غالموصل ير موجود'
				
			);
			 
		}else{
			$msg = array(
 				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
   				4 => 'password successfully updated'    ,
   				5=>  'Delivery token required'
			);
			 
		}

       
        $validator  = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
        }

        $user = Deliveries::where('delivery_id',$this->get_id($request,'deliveries','delivery_id'))
                        -> update([
                                      
                                         'password'              =>  md5($request->input('password')),
                                         'activation_code'       => null
                                 ]);

        return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
    }



public function editProfile(Request $request){
	    
		$lang = $request->input('lang');
		
		if($lang == "ar"){
		    
		   	$cat_col     = "cat_ar_name AS cat_name";
			$country_col = "country_ar_name AS country_name";
			$city_col    = "city_ar_name AS city_name";
			
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب',
				2 => 'الموصل غير موجود '
			);
		}else{
		    
		    $cat_col     = "cat_en_name AS cat_name";
			$country_col = "country_en_name AS country_name";
			$city_col    = "city_en_name AS city_name";
			
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 =>'Delivery Not Found' 
			);
		}

		if(empty($request->input('access_token'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		} 

		     $delivery_id = $this->get_id($request,'deliveries','delivery_id');
		     if($delivery_id == 0){

		     	 return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		     }

			$id = $delivery_id;
		 

		$delivery        = $this->getDeliveryData($id, $lang, $action = "get");
		$deliveryCountry = $delivery->country_id;
		$deliveryCity    = $delivery->city_id;

		$countries       = DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = '.$deliveryCountry.', true, false) AS chosen'), 'country_code')->get();

		$cities          = DB::table('city')->select('city_id', $city_col, DB::raw('IF(city_id = '.$deliveryCity.', 1, 0) AS chosen'))->get();
 	 			 											 	

		return response()->json([
									'status' => true, 
									'errNum' => 0, 
									'msg'    => $msg[0], 
									'countries' => $countries, 
									'cities'    => $cities, 
 									'delivery'  => $delivery,
 								]);
	}



	public function updateProfile(Request $request){

		     
	  //  return response() -> json($request);
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تم  تحديث الملف نجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'كلا من المدينه و الدوله يجب ان تكون ارقاما',
				3 => 'صيغة البريد الإلكترونى غير صحيحة',
				4 => 'الرقم السرى لا يجب ان يقل عن 8 حروف',
				5 => 'فشل فى رفع الصور',
				6 => 'فشل التسجيل، من فضلك حاول فى وقت لاحق',
				7 => 'البريد الإلكترونى مستخدم من قبل',
				8 => 'رقم الجوال مستخدم من قبل',
				9 => 'جميع الصوره المطلوبه يجب ان تكون فى صيغة jpeg او png',
			    10 => 'التصنيفات مطلوبه',
			    11 =>  'الدولة غير موجوده ',
				12 => 'المدينة غير موجوده',
				13 => ' صيغه الهاتف غير  صحيحه لابد انت تبدا ب 5 , 05',
				14 => 'كلمتي المرور غير متطابقتان ',
				15 => 'license_img_ext is required',
				16 => 'car_form_ext is required',
				17 => 'Insurance_img_ext is required',
				18 => 'authorization_img_ext is required',
				19 => 'national_img_ext is required',
				20 => 'السائق غير موجود ',
			);
		}else{
			$msg = array(
				0 => 'Profile Updated successfully',
				1 => 'All fields are required',
				2 => 'country and city must be numeric',
				3 => 'E-mail must be in email format',
				4 => 'Password can not be less than 8 characters',
				5 => 'Failed to upload images',
				6 => 'Failed to  update profile, please try again later',
				7 => 'Repeated email',
				8 => 'Repeated phone',
				9 => 'Requested images must be jpeg or png type',
				10 => 'Categories are required',
				11 =>  'Country not exists',
				12 => 'City not exists',
				13 => 'phone format invalid must start with 5 or 05',
				14 => 'password not confirmed',
				15 => 'license_img_ext is required',
				16 => 'car_form_ext is required',
				17 => 'Insurance_img_ext is required',
				18 => 'authorization_img_ext is required',
				19 => 'national_img_ext is required',
				20 => 'Delivery Not Found',
				

			);
		}

		$messages = array(

			'required'                           => 1,
			'numeric'                            => 2,
			'email'                              => 3,
			'min'                                => 4,
 			'phone.unique'                       => 8,
			'mimes' 		                     => 9,
			'country_id.exists'                  => 11,
			'city_id.exists'                     => 12,
			'phone.regex'                        => 13,
			'password.confirmed'                 => 14,
			'license_img_ext.required_with'      => 15,
			'car_form_ext.required_with'         => 16,
			'Insurance_img_ext'                  => 17,
			'authorization_img_ext'              => 18,
			'national_img_ext'                   => 19,
 

		);



   $rules = [
           	'access_token'      => 'required_with',
			'full_name'    		=> 'required',
			'country_id'   		=> 'required|exists:country,country_id',
			'city_id'    		=> 'required|exists:city,city_id',
			'country_code'   		=> 'required',
			'car_number'      		=> 'required',
   			'license_img'       	=> 'sometimes|nullable',
			'license_img_ext'       => 'required_with:license_img',
			'car_form_img'       	=> 'sometimes|nullable',
			'car_form_ext'          => 'required_with:car_form_img',
			'Insurance_img'       	=> 'sometimes|nullable',
			'Insurance_img_ext'     => 'required_with:Insurance_img',
			'authorization_img'     => 'sometimes|nullable',
			'authorization_img_ext' => 'required_with:authorization_img',
			'national_img'       	=> 'sometimes|nullable',
			'national_img_ext'      => 'required_with:national_img',

		];


		
		$validator = Validator::make($request->all(),$rules, $messages);

		if($validator->fails()){

			  $error = $validator->errors() ->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
	 
		}



		  $deliveryId     =  $this->get_id($request,'deliveries','delivery_id');

		        if($deliveryId == 0 ){
		              return response()->json(['status' => false, 'errNum' => 20, 'msg' => $msg[20]]);
		        }


           $inputs = $request -> only('full_name','country_id','city_id','phone','car_number');

	      $delivery = DB::table("deliveries") ->where('delivery_id',$deliveryId) -> select('phone','license_img','car_form_img','Insurance_img','authorization_img','national_img') -> first();


	      if(!$delivery)
	      {

	      	return response()->json(['status' => false, 'errNum' => 20, 'msg' => $msg[20]]);
	      }


	 if($inputs['phone'] != $delivery -> phone){

            $rules['phone']        = array('required','numeric','regex:/^(05|5)([0-9]{8})$/','unique:deliveries,phone');

             $inputs['account_activated'] = "0";
             $inputs['status']            = "0";

              $code = $this -> generate_random_number(4);

            $inputs['activation_code'] = json_encode([
                'code'   => $code,
                'expiry' => Carbon::now()->addDays(1)->timestamp,
            ]);

 
            $message = (App()->getLocale() == "en")?
                "Your Activation Code is :- " . $code :
                $code . "رقم الدخول الخاص بك هو :- " ;

            (new SmsController())->send($message , $delivery ->  phone);

            $isPhoneChanged = true;
             

        }else{

             $rules['phone'] = array('required','numeric','regex:/^(05|5)([0-9]{8})$/');

             $isPhoneChanged = false;
 
        }
 
  

    if($request -> latitude)
    {

         $inputs['latitude'] =  $request -> latitude;
    }


    if($request ->  longitude){
           
        $inputs['longitude'] =  $request -> longitude;
         
    } 


   if($request->input('license_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> license_img, $request->input('license_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['license_img']= $image;
    					}

    					 if(Storage::disk('deliveries')->exists($delivery -> license_img))
			               {
			                     
			                     Storage::disk('deliveries')->delete($delivery  -> license_img);

			               }
 
            }


   if($request->input('car_form_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> car_form_img, $request->input('car_form_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['car_form_img']= $image;
    					}

    					 if(Storage::disk('deliveries')->exists($delivery -> car_form_img))
			               {
			                     
			                     Storage::disk('deliveries')->delete($delivery  -> car_form_img);

			               }

 
            }

   if($request->input('Insurance_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> Insurance_img, $request->input('Insurance_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['Insurance_img']= $image;
    					}


    					if(Storage::disk('deliveries')->exists($delivery -> Insurance_img))
			               {
			                     
			                     Storage::disk('deliveries')->delete($delivery  -> Insurance_img);

			               }

 
            }

   if($request->input('authorization_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> authorization_img, $request->input('authorization_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['authorization_img']= $image;
    					}


    					if(Storage::disk('deliveries')->exists($delivery -> authorization_img))
			               {
			                     
			                     Storage::disk('deliveries')->delete($delivery  -> authorization_img);

			               }
 
            }

   if($request->input('national_img')){
                     
                     //save new image   64 encoded
                    $image = $this->saveImage( $request -> national_img, $request->input('national_img_ext'), 'deliveryImages/');
                                 
    					if($image == ""){
    						if($lang == "ar"){
    							$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
    						}else{
    							$errMsg = "Failed to upload image, please try again later";
    						}
    
    						return response()->json(['status'=> false, 'errNum' => 30, 'msg' => $errMsg]);
    					}else{
    					     
        	                      $inputs['national_img']= $image;
    					}

    					if(Storage::disk('deliveries')->exists($delivery -> national_img))
			               {
			                     
			                     Storage::disk('deliveries')->delete($delivery  -> national_img);

			               }
 
            }

   
 
   $inputs['country_code']    = $this -> checkCountryCodeFormate($request->input('country_code'));
    

			try {
			
				DB::transaction(function() use ($inputs, $deliveryId){
					  DB::table('deliveries')-> where('delivery_id',$deliveryId) -> update($inputs);		 
				});

				
 				 
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'isPhoneChanged' => $isPhoneChanged]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
			}
	}




public function newOrders(Request $request){


	$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون 1 او 2 او 3 او 4',
				4 => 'لا يوجد طلبات بعد',
				5 =>  'الموصل  غير موجود ',
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
				5 => 'delivery not Found'
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

		// 1 -> new orders 2-> current accepted  orders 3-> cancelled orders + deliveried orders


		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		 $delivery_id = $this->get_id($request,'deliveries','delivery_id');

		        if($delivery_id == 0 ){
		              return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		        }

		      $check = DB::table('deliveries')   -> where('delivery_id',$delivery_id) -> first();

		      if(!$check){
		      	return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		      }
  


 			$type 		  = $request->input('type');
			
			$conditions[] = ['orders_headers.delivery_method','=',3];  // delivery method  is "by delivery"

 
			$inCondition = [];
			if($type == 1){
			   
				$inCondition = [2];
				$conditions[] = ['orders_headers.delivery_id','=',0];  // delivery method  is "by 
			  
			//  array_push($conditions, [DB::raw('orders_headers.created_at') , '>', Carbon::now()->addHours(1)->subMinutes($time_counter_in_min)]);
				 
			}elseif($type == 2){
				$inCondition = [2];
				$conditions[] = ['orders_headers.order_id','>',0];
				//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
			}elseif($type == 3){
				$inCondition = [1,2,3];
				$conditions[] = ['orders_headers.order_id','>',0];
				//$conditions[] = ['orders_headers.delivery_id','=',$delivery_id];
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
                                     'orders_headers.total_value'
                          )
                        
						->orderBy('orders_headers.order_id', 'DESC')
						->paginate(10); 


              // if return new orders 
              if($type == 1){
 
 					if(isset($orders) && $orders -> count() > 0){                       

                       foreach ($orders as $key => $order){

                           $approvedBefore = DB::table('rejectedorders_delivery') -> where('order_id',$order -> order_id) -> where('status',1)-> first();

 
                           $cancelFromThisDeliveryBefore = DB::table('rejectedorders_delivery') -> where('order_id',$order -> order_id) -> where('delivery_id',$delivery_id) -> where('status',0)-> first();

  
                           if ($approvedBefore or $cancelFromThisDeliveryBefore) {
                           	 
                                  $orders -> forget($key);
                                 
                           }

                            $order -> status = " بأنتظار الموافقه ";
                             unset($order -> delivery_id);  
        
						   
						}
                            
					}
			}		


     // current orders  
	   if($type == 2){

           if(isset($orders) && $orders -> count() > 0){                       
                       foreach ($orders as $key => $order){

                             $approvedBefore = DB::table('rejectedorders_delivery') -> where('order_id',$order -> order_id) -> where('status',1)-> where('delivery_id',$delivery_id) ->  first();

                             if(!$approvedBefore){

                                  $orders -> forget($key);
                                   
                             }

                             $order -> status = "موافق عليه ";
                              unset($order -> delivery_id);  
						}                            
					}
	   }


	    // previous  orders (cancelled by delivery + deliveried by delivery ) 
	   if($type == 3){

           if(isset($orders) && $orders -> count() > 0){  
 
                       foreach ($orders as $key => $order){
 
                           $cancelFromThisDeliveryBefore = DB::table('rejectedorders_delivery') ->where([
                                    'order_id'     => $order -> order_id,
                                    'delivery_id' =>$delivery_id

                           ]) -> whereIn('status',[0,2])-> first();

                           if(!$cancelFromThisDeliveryBefore){

                                   $orders -> forget($key);
                                   
                           }

                          if($order -> delivery_id == 0 ) {
                            $order -> status = "ملغي";
                        }
                           else{
                           	$order -> status = "تم التسليم ";
                           	}

                           unset($order -> delivery_id);  
  
						}
                    
					}

	   }			

			

			return response()->json([
										'status' 			    => true,
										'errNum' 			    => 0,
										'msg' 				    => $msg[0],
										'orders' 			    => $orders,
										 
									]);
}


public function OrderDetails(Request $request){


	$lang = $request->input('lang');

		if($lang == "ar"){
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد تفاصيل ',
				2 => 'رقم   الطلب  مطلوب',
				3 => 'الطلب غير موجود ',
				4 => ' الموصل  غير موجود '
			);
			$payment_col = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
 			$status_col = 'order_status.ar_desc AS order_status';
		}else{
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no order details!!',
				2 => 'order_id is required',
				3 => 'order not exists',
				4 => 'Delivery Not Exists'
 			);
			$payment_col = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
 			$status_col = 'order_status.en_desc AS order_status';
		}
		

		$messages = array(
			'required'            => 2,
			'exists'              => 3,
 
		);
 
		$validator = Validator::make($request->all(), [
			'order_id'       => 'required|exists:orders_headers,order_id',
 
		], $messages);

		if($validator->fails()){
			$errors   = $validator->errors();
			$error    = $errors->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		} 
		       //get order header
	    	$order = DB::table('orders_headers')
                    ->where('orders_headers.order_id', $request->input('order_id'))
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->join('users', 'orders_headers.user_id' ,'=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->select(  'orders_headers.order_code', 
						       'orders_headers.order_id',
						       'orders_headers.status_id',
						       'orders_headers.status_id',
                               $status_col,
						       'orders_headers.total_value AS total',
						       'orders_headers.net_value AS net_value',   // order price with out any delivery price just products with options 
						       'orders_headers.app_value AS app_value',
						       'orders_headers.delivery_price',
						        'orders_headers.delivery_app_value',
						        'orders_headers.delivery_app_percentage',
						        'orders_headers.app_percentage',
						       'orders_headers.total_discount',
						       'orders_headers.address as user_address',
						       'orders_headers.user_longitude', 
						       'orders_headers.user_latitude',
						       'orders_headers.user_phone',
						       'orders_headers.user_email', 
						        DB::raw('IFNULL(orders_headers.delivered_at, "") AS delivered_at'),
						     $payment_col, 
						      $delivery_col,
						      'orders_headers.delivery_method AS delivery_method_id',
						      'providers.store_name AS store_name',
						      'users.full_name AS user_name',
						      DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',providers.profile_pic) AS store_image"),
						        'providers.longitude AS provider_longitude', 
						        'providers.latitude AS provider_latitude',
						        'providers.membership_id',
						         DB::raw('IFNULL(DATE(orders_headers.created_at), "") AS order_date'),
					             DB::raw('IFNULL(TIME(orders_headers.created_at), "") AS order_time'),
					             DB::raw('IF(orders_headers.delivery_id = 0, 0, 1) AS allowed')

					         )
					->first();
					
					
				//	dd($header);

		$products = DB::table('order_products')->where('order_products.order_id', $request->input('order_id'))
					 ->join('products', 'order_products.product_id', '=', 'products.id')
					 ->select(
					            'order_products.qty',
                                'products.title',
                                'products.description',
                                'order_products.product_price',
                                'order_products.discount',
                                'products.id as product_id'
                     )
					 ->get();


			if(isset($products) && $products -> count() > 0){
                
                foreach ($products as  $product) {
                	  
                     $image = DB::table('product_images') -> where('product_id',$product -> product_id) -> first();

                     if($image){

                     	 $product -> main_image =  env('APP_URL').'/public/providerProfileImages/'.$image -> image;

                     }else{
                      
                         $product -> main_image ="";

                     }

                }
 
			} 

        //return response()->json(["dataa" , $details]);

		if($order){
			$status = $order->status_id;
		}else{
			$status = "";
		}


		   //get rate only if order status is deliveried

		if($status == 3 || $status == "3"){

			$provider_order_rate = DB::table('provider_evaluation')
                            ->where('order_id',$request->input('order_id'))
						    ->select(
						    	DB::raw("IFNULL(((quality + autotype + packing + maturity + ask_again) / 5), 0) AS order_rate") , 
						    	DB::raw("IFNULL(((comment)), 0) AS comment"))
						    ->first();

			if($provider_order_rate){
                $provider_order_rate = [
                                        "rate" => $provider_order_rate->order_rate ,
                                        "comment" => $provider_order_rate->comment
                                        ];
			}else{
                $provider_order_rate = "";
			}

		}else{
            $provider_order_rate = "";
		}
		  
		$order_status = DB::table('order_status')->whereIn('status_id', [1,2,3,4])
						   ->select(
						   	'status_id', 
						   	$status_col,
						   	 DB::raw('IF(status_id = '.$order -> status_id.', true, false) AS choosen')
						   )->get();
 
		$percentage = DB::table('app_settings')->select('app_percentage')->first();

		if($percentage){
			$app_percentage = $percentage->app_percentage;
		}else{
			$app_percentage = 0;
		}
 
		return response()->json([
		                            'status'    => true,
                                    'errNum'    => 0, 
                                    'msg'       =>'Retrieved successfully',
                                    'order'     => $order,
                                    'products'  => $products,
                                    'app_percentage'      => $app_percentage,
                                    //'order_status'        => $order_status,
                                     'provider_order_rate' => $provider_order_rate,

                                ]);


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
				3 => 'رقم الحالة يجب ان يكون  0 او 1 او 2',
				4 => 'رقم الموصل مطلوب إذا كان رقم الحاله = 3 و طريقة التوصيل = 1',
				5 => 'رقم الموصل خطأ',
				6 => 'التاجر  غير موجود ',
				7 => 'الموصل غير موجود ',
				8 => 'عفوا لقدم تم الموافقه علي هذا الطلب من قبل موصل اخر ',
				9 => 'عفوا لا يمكن توصيل هذا الطلب ',
				10 => 'عفوا لقد تم قبول الطلب من قبلكم مسبقا ',
				11 => 'لابد من الموافقه علي الطلب اولا ومن قم الغاءه ',
				12 => 'لايمكنك هذا الاجراء جيث تم العاء هذا الطلب من قبل ',
				13 => 'عفوا  لقد تم الغاء الطلب مسبقا ',
				14 => 'لابد من الموافقه علي الطلب اولا ',
				15 =>'لا يمكن تسليم هذا الطلب '

			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'All fields are required',
				2 => 'Process failed, please try again later',
				3 => 'status_id must be 0 ,1 or 2',
				4 => 'delivery id is required if status id = 3 AND delivery_method = 1',
				5 => 'Invalid delivery_id',
				6 => 'Provider not exists',
				7 => 'delivery not exists',
				8 => 'Sorry Order Approved by another delivery',
				9 => 'Sorry cann\'t deliver this order' ,
				10 => 'Sorry you accept order before',
				11 => 'Sorry You cannot cancel order before approved it firstly',
				12 => 'Sorry You cannot do this operation because you cancelled this order before',
				13 => 'Sorry You cancelled Order Before',
				14 =>'You Must accept order First',
				15 =>'Cannot deliver this order'
			);
		}

		$messages = array(
			'required'           => 1,
			'in'                 => 3,
			'access_token.exists'=> 7,
		);

		$validator = Validator::make($request->all(), [
			'order_id'         => 'required',
			'access_token'     => 'required|exists:deliveries,token',
			'status_id'        => 'required|in:0,1,2'

		], $messages);

		if($validator->fails()){


			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}

		$order_id        = $request->input('order_id');
		$status          = $request->input('status_id');
		$delivery_id     = $this->get_id($request,'deliveries','delivery_id');
		$updates         =[];
 	    $updates['delivery_id'] =  $delivery_id;
 	    $notif_data = array();
     	  	 
		$get = DB::table('orders_headers')
		    ->join('providers','providers.provider_id','=','orders_headers.provider_id')
		    ->join('users','users.user_id','=','orders_headers.user_id')
            ->where('order_id', $order_id)
            ->select('delivery_method', 'delivery_id','status_id','orders_headers.user_id','orders_headers.provider_id','providers.device_reg_id as provider_device_reg','users.device_reg_id as user_device_reg')
            ->first();


            if($get -> delivery_method !=  3){

            	return response()->json(['status' => false, 'errNum' => 9 , 'msg' => $msg[9]]);
            }



     
        if($status == 1)
        {

            if($get -> delivery_id != 0){

	            if($get -> delivery_id ==  $delivery_id){
	 			     return response()->json(['status' => false, 'errNum' => 10 , 'msg' => $msg[10]]);
	 	        	}else{
	              
	                  return response()->json(['status' => false, 'errNum' => 8 , 'msg' => $msg[8]]);
 		 		 }
	 		  }else{

                
	 		  	$acceptedBefore =  DB::table('rejectedorders_delivery') -> where([
	 		  	 	'order_id'     => $order_id,
	 		  	 	'delivery_id'  => $delivery_id	 		  	 	 

	 		  	  ]) ->  select('status')-> first();


                if($acceptedBefore){

                  if($acceptedBefore -> status  == 0 )

	 		  	   return response()->json(['status' => false, 'errNum' => 12 , 'msg' => $msg[12]]);

		 		  	elseif($acceptedBefore -> status  == 1){
	                   
	                   return response()->json(['status' => false, 'errNum' => 10 , 'msg' => $msg[10]]);

		 		  	 }

	 		  	 }


	 		  	 DB::table('rejectedorders_delivery') -> insert(['order_id' => $order_id,'delivery_id' => $delivery_id,"status" => 1]);

	             DB::table('orders_headers')->where('order_id', $order_id)
					  						  ->update($updates);		  						  

			  
				if($lang == 'ar'){
					$push_notif_title   ='موافقه الموصل ';
					$push_notif_message = "قد نم قبول الطلب  {$order_id}من الموصل  {$delivery_id}";
				}else{
					$push_notif_title   ='Delivery accepted';
					$push_notif_message = "order number {$order_id}  accepted by delivey id {$delivery_id}";
				}
					  						  

			     
	 		}  

 		}elseif($status == 0){
                
                // here delivery cancel orders 

 			   //send notify to order's provider and user
 				if($lang == 'ar'){
					$push_notif_title   ='رفض الموصل ';
					$push_notif_message = "لقد نم  رفض الطلب  {order_id}من الموصل  {$delivery_id}";
				}else{
					$push_notif_title   ='Delivery cancelled';
					$push_notif_message = "order number {$order_id}  cancelled by delivey id {$delivery_id}";
				}
			 


              if($get -> delivery_id != $delivery_id){

                return response()->json(['status' => false, 'errNum' => 11 , 'msg' => $msg[11]]);

              }

           
           	$rejectedBefore =  DB::table('rejectedorders_delivery') -> where([
	 		  	 	'order_id'     => $order_id,
	 		  	 	'delivery_id'  => $delivery_id

	 		  	  ]) -> select('status') -> first();


                if($rejectedBefore){

                	if($rejectedBefore -> status == 0){

		 		  	   return response()->json(['status' => false, 'errNum' => 13 , 'msg' => $msg[13]]);
		 		     	}elseif($rejectedBefore -> status == 1){

		 		     			$rejectedBefore =  DB::table('rejectedorders_delivery') -> where([
				 		  	 	'order_id'     => $order_id,
				 		  	 	'delivery_id'  => $delivery_id

				 		  	  ]) -> update(['status' => 0]);

		 		     			 DB::table('orders_headers')->where('order_id', $order_id)
								  						  ->update(['delivery_id'=> 0]);

 

		 		     	}

		 		  	 }else{


			          DB::table('orders_headers')->where('order_id', $order_id)
								  						  ->update(['delivery_id'=> 0]);
					   DB::table('rejectedorders_delivery')->insert([

					   	  'order_id'      => $order_id,
					   	  'delivery_id'   => $delivery_id,
					   	  'status'        => 0,
			 
					   ]);	
		 		  	 }
 
 		}else{
 
 

            //order deliveried
            if($status == 2){
               
                $checkIfApprovedBefore = DB::table('rejectedorders_delivery') -> where([
	 		  	 	'order_id'     => $order_id,
	 		  	 	'delivery_id'  => $delivery_id

	 		  	  ]) -> select('status') -> first();

                if($checkIfApprovedBefore){

                	if($checkIfApprovedBefore -> status != 1){

                       return response()->json(['status' => false, 'errNum' => 14 , 'msg' => $msg[14]]);
                	}


                }else{

                	return response()->json(['status' => false, 'errNum' => 15 , 'msg' => $msg[15]]);

                }

 
                  DB::table('rejectedorders_delivery') -> where([
	 		  	 	'order_id'     => $order_id,
	 		  	 	'delivery_id'  => $delivery_id

	 		  	  ]) -> update(['status' => 2 ]);


                     //update order status to provider to 3 // delivered status
	 		  	  DB::table('orders_headers') -> where('order_id',$order_id) -> update(['status_id' => 3]);


	 		  	  if($lang == 'ar'){
					$push_notif_title   ='تم توصيل الطلب ';
					$push_notif_message = "تم توصيل الطلب برقم  {$order_id}من الموصل  {$delivery_id}";
				}else{
					$push_notif_title   ='Order Deliveried';
					$push_notif_message = "order number {$order_id}  has been delivered by delivery  {$delivery_id}";
				}

            }


 		}
	 		    

 //send notify to order's provider and user
				
				$notif_data['title']      = $push_notif_title;
			    $notif_data['message']    = $push_notif_message;
			    $notif_data['order_id']   = $order_id;
			    $notif_data['notif_type'] = 'order';
			    
			    
			 (new Push())->send($get->provider_device_reg,$notif_data,(new Push())->provider_key);

			 (new Push())->send($get->user_device_reg,$notif_data,(new Push())->user_key);
			     
  
			      DB::table("notifications")
		            ->insert([
		                "en_title"           => $notif_data['title'],
		                "ar_title"           => $notif_data['title'],
		                "en_content"         => $notif_data['message'],
		                "ar_content"         => $notif_data['message'],
		                "notification_type"  => 1,
		                "actor_id"           => $get->user_id,
		                "actor_type"         => "user",
		                "action_id"          => $order_id

		            ]);


                    DB::table("notifications")
		            ->insert([
		                "en_title"           => $notif_data['title'],
		                "ar_title"           => $notif_data['title'],
		                "en_content"         => $notif_data['message'],
		                "ar_content"         => $notif_data['message'],
		                "notification_type"  => 1,
		                "actor_id"           => $get->provider_id,
		                "actor_type"         => "provider",
		                "action_id"          => $order_id

		            ]);

  

  return response()->json(['status' => false, 'errNum' => 0 , 'msg' => $msg[0]]);

			 
	}














	public function fetchOrdersCounts(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required'
			);
		}

		if(empty($request->input('delivery_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$delivery_id = $request->input('delivery_id');
		}

		
		$today = date("Y-m-d");
		//get new orders count 
		$news    = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->where('status_id', 3)
										   ->count();

		$current = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [8])
										   ->where(DB::raw('DATE(expected_delivery_time)'), '<=',$today)
										   ->count();

		$futures = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [8])
										   ->where(DB::raw('DATE(expected_delivery_time)'), '>' ,$today)
										   ->count();

		$old = DB::table('orders_headers')->where('delivery_id', $delivery_id)
										   ->whereIn('status_id', [4,5,6,7])
										   ->count();

		$complains = DB::table('complains')->where('delivery_id', $delivery_id)->count();
		return response()->json([
									'status' => true, 
									'errNum' => 0, 
									'msg'    => $msg[0],
									'new_orders_count'     => $news,
									'current_orders_count' => $current,
									'future_orders_count'  => $futures,
									'old_orders_count'     => $old,
									'complains_count'      => $complains
								]);
	}

	public function getComplains(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم م قدم الخدمه مطلبو',
				2 => 'من فضلك حدد الشكوى من من',
				3 => 'الشكوى من يجب ان كتون فى (delivery, user, both)'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required',
				2 => 'complain_from is required',
				3 => 'complain_from must be in (provider, user, both)'
			);
		}

		if(empty($request->input('delivery_id'))){
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}else{
			$delivery_id = $request->input('delivery_id');
		}

		if(empty($request->input('complain_from'))){
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);	
		}else{
			$complain_from = $request->input('complain_from');
		}

		if(!in_array($complain_from, array('provider', 'user', 'both'))){
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);	
		}
		$jointable    = "";
		$joincol1     = "";
		$joincol2     = "";
		$selectedCol  = "";
		$nullCol      = "";
		$conditions[] = array("complains.delivery_id", "=", $delivery_id);
		if($complain_from == "provider"){
			$conditions[] = array("complains.provider_id", "!=", 0);
			$conditions[] = array("complains.user_id", "=", 0);
			$jointable    = "providers";
			$joincol1     = "complains.provider_id";
			$joincol2     = "providers.provider_id";
			$selectedCol  = "IFNULL(providers.brand_name, '') AS provider_name";
			$nullCol      = "'' AS user_name";
		}elseif($complain_from == "user"){
			$conditions[] = array("complains.provider_id", "=", 0);
			$conditions[] = array("complains.user_id", "!=", 0);
			$jointable    = "users";
			$joincol1     = "complains.user_id";
			$joincol2     = "users.user_id";
			$selectedCol  = "IFNULL(users.full_name,'') AS user_name";
			$nullCol      = "'' AS provider_name";
		}
		$data = array();
		//get provider complains
		if($complain_from != 'both'){
			$complains = DB::table('complains')->where($conditions)
						    ->join($jointable, $joincol1, '=', $joincol2)
						    ->join('orders_headers', 'complains.order_id', '=', 'orders_headers.order_id')
						    ->select('complains.order_id', 'orders_headers.order_id', 'orders_headers.order_code', DB::raw($selectedCol), DB::raw($nullCol), 'complains.complain','complains.attach_no', 'complains.id')
						    ->get();
		}else{
			$complains = DB::table('complains')->where('complains.delivery_id', $delivery_id)
						    ->leftjoin('users', 'complains.user_id', '=', 'users.user_id')
						    ->leftjoin('providers', 'complains.provider_id', '=', 'providers.provider_id')
						    ->join('orders_headers', 'complains.order_id', '=', 'orders_headers.order_id')
						    ->select('complains.order_id', 'orders_headers.order_id', 'orders_headers.order_code', DB::raw('IFNULL(users.full_name, "") AS user_name'), DB::raw('IFNULL(providers.brand_name, "") AS provider_name'),'complains.complain','complains.attach_no', 'complains.id')
						    ->get();
		}

		if($complains->count()){
			foreach($complains AS $row){
				//get Attaches 
				$attaches = array();
				if($row->attach_no != 0 && $row->attach_no != "0"){
					$getAttaches = DB::table('attachments')->where('attach_id', $row->attach_no)
									 ->select('id','attach_path', 'type')
									 ->get();
					$attaches = $getAttaches;
				}

				array_push($data, ['user_name' => $row->user_name, 'provider_name' => $row->provider_name,'order_id' => $row->order_id, 'order_code' => $row->order_code, 'complain' => $row->complain, 'attaches' => $attaches]);
			}
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'data' => $data]);
	}

	


	public function getDeliveryOrders(Request $request){
		$lang     = $request->input('lang');
		$allPages = $request->input('allPages');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون 1 او 2 او 4',
				4 => 'لا يوجد طلبات بعد',
				5 => 'رقم الموصل غير صحيح'
			);
			$payment_col  = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col	  = "order_status.ar_desc AS status_text";
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required',
				2 => 'type is required',
				3 => 'type must be 1, 2 or 4',
				4 => 'There is no ordes yet',
				5 => 'Invalid delivery id'
			);
			$payment_col  = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col	  = "order_status.en_desc AS status_text";
		}

		$messages  = array(
			'delivery_id.required' => 1,
			'type.required'        => 2,
			'in'                   => 3,
			'exists'			   => 5
		);
		$validator = Validator::make($request->all(), [
			'delivery_id' => 'required|exists:deliveries,delivery_id',
			'type'        => 'required|in:1,2,4'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$delivery_id  = $request->input('delivery_id');
			$type 		  = $request->input('type');
			$today        = date('Y-m-d');
			$conditions[] = ['deliveries.delivery_id','=', $delivery_id];
			$inCondition  = [];
//			if($type == 1){
//				$inCondition = [3];
//				// array_push($conditions, ['orders_headers.status_id' , '=', 3]);
//			}elseif($type == 2){
//				$inCondition = [8];
//				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
//				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
//				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
//			}elseif($type == 3){
//				$inCondition = [8];
//				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
//				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
//				array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '>', $today]);
//			}else{
//				$inCondition = [4,5,6,7];
//				// array_push($conditions, ['orders_headers.status_id' , '=', 6]);
//			}
			if($type == 1){
				$inCondition = [3];
				// array_push($conditions, ['orders_headers.status_id' , '=', 3]);
			}elseif($type == 2){
				$inCondition = [8];
				// array_push($conditions, ['orders_headers.status_id' , '>', 3]);
				// array_push($conditions, ['orders_headers.status_id' , '!=', 6]);
				//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
			}else{
				$inCondition = [4,5,6,7];
				// array_push($conditions, ['orders_headers.status_id' , '=', 6]);
			}

			

			//get orders
			if(empty($allPages) || $allPages == "0" || $allPages == 0){
				$orders = DB::table('orders_headers')
                            ->where($conditions)
							->whereIn('orders_headers.status_id', $inCondition)
						    ->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
						    ->join('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
						    ->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
						    ->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
						    ->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
						    ->select('orders_headers.user_longitude','orders_headers.user_latitude','orders_headers.order_id','providers.brand_name AS provider_name', 'orders_headers.address', $payment_col, $delivery_col, DB::raw("(SELECT count(order_details.id) FROM order_details WHERE order_details.order_id = orders_headers.order_id) AS meals_count"), $status_col,DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.transfer_to_delivery_at) AS created_time'))
						    ->orderBy('orders_headers.order_id', 'DESC')
						    ->paginate(10);
			}else{
				$orders['data'] = DB::table('orders_headers')->where($conditions)
							->whereIn('orders_headers.status_id', $inCondition)
							->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
							->join('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
							->join('delivery_methods', 'orders_headers.delivery_method' ,'=', 'delivery_methods.method_id')
							->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
							->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
							->select('orders_headers.user_longitude','orders_headers.user_latitude','orders_headers.order_id','providers.brand_name AS provider_name', 'orders_headers.address', $payment_col, $delivery_col, DB::raw("(SELECT count(order_details.id) FROM order_details WHERE order_details.order_id = orders_headers.order_id) AS meals_count"), $status_col,DB::raw('DATE(orders_headers.created_at) AS created_date'), DB::raw('TIME(orders_headers.transfer_to_delivery_at) AS created_time'))
							->orderBy('orders_headers.order_id', 'DESC')
							->get();
			}

			//get allowed time to accept the order
			if($type == 1){
				$get_time_counter = DB::table("app_settings")->first();
				if($get_time_counter != NULL){
					$time_counter_in_hours  = ($get_time_counter->max_time_to_accept_order) / 60;
					$time_counter_in_min    = $get_time_counter->max_time_to_accept_order;
				}else{
					$time_counter_in_hours = 0;
					$time_counter_in_min   = 0;
				}
			}else{
				$time_counter_in_hours = 0;
				$time_counter_in_min   = 0;
			}

			$today_date = date('Y-m-d');
			$now        = date('h:i:s');
			return response()->json([
										'status' 			    => true, 
										'errNum' 			    => 0, 
										'msg' 				    => $msg[0],
										'orders' 			    => $orders,
										'time_counter_in_min'   => $time_counter_in_min,
										'time_counter_in_hours' => $time_counter_in_hours,
										'today_date' 			=> $today_date,
										'now' 					=> $now
									]);
		}
	}

	public function orderAcceptance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العمليه بنجاح',
				1 => 'رقم الطلب مطلوب',
				2 => 'رقم الموصل مطلوب',
				3 => 'نوع العمليه مطلوب',
				4 => 'نوع العمليه يجب ان يكون (accept or reject)',
				5 => 'فشلت العمليه من فضلك حاول فى وقت لاحق'
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'order_id is required',
				2 => 'delivery_id is required',
				3 => 'type is required',
				4 => 'type must be (accept or reject)',
				5 => 'Process failed please try again later'
			);
		}

		$messages = array(
			'order_id.required'    => 1,
			'delivery_id.required' => 2, 
			'type.required'        => 3,
			'in' 				   => 4
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required', 
			'delivery_id' => 'required', 
			'type'        => 'required|in:accept,reject',
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$type = $request->input('type');
			
			if($type == "accept"){
				$status = 8;
				if($lang == "ar"){
				    $notify_title = "تم قبول الطلب";
    				$notify_message = "تم قبول الطلب من قبل الموصل";   
				}else{
				    $notify_title = "delivery accept order";
				    $notify_message = "delivery accepted this order";
				}
			}else{
				$status = 2;
				if($lang == "ar"){
				    $notify_title = "رفض الطلب";
    				$notify_message = "تم رفض الطلب من قبل الموصل";    
				}else{
				    $notify_title = "reject order from delivery";
				    $notify_message = "delivery rejected this order";
				}
			}
			try {
				$delivery_id = $request->input('delivery_id');
				$order_id    = $request->input('order_id');
				DB::transaction(function() use ($status, $order_id, $delivery_id){
					DB::table("orders_headers")->where('order_id', $order_id)->update(['status_id' => $status]);
					DB::table("order_details")->where('order_id', $order_id)->update(['status' => $status]);
				});
				$notif_data = array();
				$notif_data['title']      = $notify_title;
			    $notif_data['message']    = $notify_message;
			    $notif_data['order_id']   = $order_id;
			    $provider_data = DB::table("orders_headers")
			                        ->join("providers" , "providers.provider_id" , "orders_headers.provider_id")
			                        ->where("orders_headers.order_id" , $order_id)
			                        ->select("providers.device_reg_id")
			                        ->first();

			    if($provider_data != null){
			        if($provider_data->device_reg_id != null){
			            $push_notif = $this->singleSend($provider_data->device_reg_id, $notif_data, $this->provider_key);
			        }
			    }
				
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} catch (Exception $e) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}
		}
	}

	public function getDeliveryBalance(Request $request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => '',
				1 => 'رقم الموصل مطلوب'
			);
		}else{
			$msg = array(
				0 => '',
				1 => 'delivery_id is required'
			);
		}

		$messages = array(
			'required' => 1
		);

		$validator = Validator::make($request->all(), [
			'delivery_id' => 'required'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
		    $balance = DB::table('balances')
                ->where('actor_id', $request->input('delivery_id'))
                ->where('type', 'delivery')
                ->select('due_balance', 'current_balance', 'forbidden_balance')
                ->first();
            //get current balance
            $current = DB::table('orders_headers')
                ->where('payment_type', 2)
                ->where('status_id', 4)
                ->where('delivery_id', $request->input('delivery_id'))
                ->where('delivery_balance_status', 1)
                ->where('delivery_complain_flag', 0)
                ->sum(DB::raw('delivery_price - delivery_app_value'));

            //get due balance
            $due = DB::table('orders_headers')
                ->where('payment_type', 1)
                ->where('status_id', 4)
                ->where('provider_id', $request->input('provider_id'))
                ->where('delivery_balance_status', 1)
                ->sum('delivery_app_value');

            //forbidden balance
            $forbidden = DB::table('orders_headers')
                ->where('payment_type', 2)
                ->where('status_id', 4)
                ->where('provider_id', $request->input('provider_id'))
                ->where('delivery_balance_status', 1)
                ->where('delivery_complain_flag', 1)
                ->sum(DB::raw('delivery_price - delivery_app_value'));

            // delivery bank data
            // check if the user has bank data
            $delivery_bank = DB::table("withdraw_balance")
                        ->select("*")
                        ->where("actor_id" , $request->input("delivery_id"))
                        ->where("type" , "delivery")
                        ->get();
            if($delivery_bank !== null && count($delivery_bank) != 0){
                $last_entry = $delivery_bank[count($delivery_bank) -1]; 
                $bank_name = $last_entry->bank_name;
                $bank_phone = $last_entry->phone;
                $bank_username = $last_entry->name;
                $bank_account_num = $last_entry->account_num;
            }else{
                $bank_name = "";
                $bank_phone = "";
                $bank_username = "";
                $bank_account_num = "";
            }
            date_default_timezone_set('Asia/Riyadh');
            $timestamp =  date("Y/m/d H:i:s", time());
            $balance = array('current_balance' => $current, 'due_balance' => $due, 'forbidden_balance' => $forbidden , "updated_at" => $timestamp);
            return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'balance' => $balance, "bank_name" => $bank_name , "bank_phone" => $bank_phone,"account_num" => $bank_account_num  , "bank_username" => $bank_username]);
		}
	}

	public function withdraw(Request$request){
		$lang = $request->input('lang');
		if($lang == "ar"){
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'رقم الموصل مطلوب',
				2 => 'الرصيد الحالى مطلوب',
				3 => 'الرصيد المستحق مطلوب',
				4 => 'فشلت العملية من فضلك حاول لاحقا',
				5 => 'لديك طلبات لم يتم الرد عليها بعد',
				6 => 'ادخل رقم الرصيد المستحق المراد سحبة',
				7 => 'current_balance يجب ان يكون رقم',
                8 => 'ليس لديك رصيد كافى لاتمام هذة العملية',
                9 => 'رصيدك الحالى اقل من الحد الادنى لسحب الرصيد',
                10 => 'رقم الرصيد الحالى مطلوب',
                11 => 'الاسم مطلوب',
                12 => 'رقم الحساب مطلوب',
                13 => 'رقم الهاتف مطلوب',
			);
		}else{
			$msg = array(
				0 => 'Process done successfully',
				1 => 'delivery_id is required',
				2 => 'current_balance is required',
				3 => 'due_balance is required',
				4 => 'Process failed, please try again later',
				5 => 'You already have pending requests',
				6 => 'Enter a valid current_balance number',
				7 => 'current_balance must be a number',
                8 => "You Don't have enough balance",
				9 => "Your balance is less than minimum balance to withdraw",
                10 => 'bank_name is required',
                11 => 'name is required',
                12 => 'account_num is required',
                13 => 'phone is required',
                14 => 'forbidden_balance is required',
                15 => 'forbidden_balance must be a number'
			);
		}

		$messages = array(
			'delivery_id.required'     => 1,
			'current_balance.required' => 2,
			'current_balance.min'      => 6,
			'current_balance.numeric'  => 7,
			'due_balance.required'     => 3,
            'bank_name.required'       => 10,
            'name.required'            => 11,
            'account_num.required'     => 12,
            'phone.required'           => 13,
            'forbidden_balance.required' => 14,
            "forbidden_balance.numeric" => 15
		);

		$validator = Validator::make($request->all(), [
			'delivery_id'     => 'required',
			'current_balance' => 'required|numeric',
			'due_balance'     => 'required',
            'bank_name'       => 'required',
            'name'            => 'required',
            'account_num'     => 'required',
            'phone' 	      => 'required' , 
            'forbidden_balance' => 'required|numeric'

		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{

			//check if there is pending requests
			$check  = DB::table('withdraw_balance')->where('actor_id', $request->input('delivery_id'))
												   ->where('type', 'delivery')
												   ->where('status', 1)
												   ->first();

            // insert bank account data into database
            $actor_bank_data = DB::table("withdraw_balance")
                ->where("actor_id" , $request->input("delivery_id"))
                ->where("type" , "delivery")
                ->first();
            // if($actor_bank_data !== null){
            //     // update bank data
            //     DB::table("withdraw_balance")
            //         ->where("actor_id" , $request->input("delivery_id"))
            //         ->where("type" , "delivery")
            //         ->update([
            //             "name" => $request->input("name"),
            //             "phone" => $request->input("phone"),
            //             "bank_name" => $request->input("bank_name"),
            //             "account_num" => $request->input("account_num"),
            //             "updated_at" =>date('Y-m-d h:i:s')
            //         ]);

            // }else{
            //     // insert bank data
            //     DB::table("withdraw_balance")
            //         ->insert([
            //             "actor_id" => $request->input("delivery_id"),
            //             "type" => "delivery",
            //             "name" => $request->input("name"),
            //             "phone" => $request->input("phone"),
            //             "bank_name" => $request->input("bank_name"),
            //             "account_num" => $request->input("account_num"),
            //             "created_at" =>date('Y-m-d h:i:s')
            //         ]);
            // }
            if($request->input("current_balance") < 0.1){
                return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
            }
			if($check != NULL){
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

            // check if the user requested blance is avaliable
            $delivery_balace = DB::table("balances")
                ->select("current_balance")
                ->where("actor_id" , $request->input("delivery_id"))
                ->where("type" , "delivery")
                ->first();
            $delivery_current_balace = $delivery_balace->current_balance;

            if($request->input("current_balance") > $delivery_current_balace){
                return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
            }


            //check if the current balance is greater than min limit of withdrawing
            $min_balance = DB::table("app_settings")
                ->select("min_balace_to_withdraw")
                ->first();
            if($request->input("current_balance") < $min_balance->min_balace_to_withdraw){
                return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
            }


			$insert = DB::table("withdraw_balance")->insert([
						 'actor_id'        => $request->input('delivery_id'),
						 'current_balance' => $request->input('current_balance'),
						 'due_balance'     => $request->input('due_balance'),
                         'forbidden'       => $request->input('forbidden_balance'),
                         'status'          =>  1,
                         'bank_name' 	   => $request->input('bank_name'),
                         'name' 		   => $request->input('name'),
                         'account_num' 	   => $request->input('account_num'),
                         'phone' 		   => $request->input('phone'),
						 'type' 		   => 'delivery'
					  ]);
			if($insert){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	public function receiveOrderSwitch(Request $request){
		$lang = $request->input('lang');
		$switch = $request->input('switch');
		if($lang == "ar"){
			if($switch == 0){
				$m = "تم إيقاف إستلام الطلبات";
			}elseif($switch == 1){
				$m = "تم تفعيل إستلام الطلبات";
			}else{
				$m = "تمت العمليه بنجاح";
			}
			$msg = array(
				0  => $m,
				1  => 'كل الحقول مطلوبه',
				2  => 'قيمة السويتش يجب ان تنحصر بين 0 و 1',
				3  => 'فشلت العمليه'
			);
		}else{
			if($switch == 0){
				$m = "Receiving orders deactivated";
			}elseif($switch == 1){
				$m = "Receiving orders activated";
			}else{
				$m = "Process done successfully";
			}
			$msg = array(
				0  => $m,
				1  => 'All fields are required',
				2  => 'Switch value must be between 0 and 1',
				3  => 'Process failed',
			);
		}

		$messages = array(
			'required' => 1,
			'in'	   => 2
		);

		$validator = Validator::make($request->all(), [
			'delivery_id' 		=> 'required',
			'switch'			=> 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			$check = Deliveries::where('delivery_id', $request->input('delivery_id'))
							  ->update(['receive_orders' => $switch]);
			if($check){
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}
	}

	public function orderFinalAction(Request $request){
		$lang = $request->input('lang');
		$type = $request->input('type');
		if($lang == "ar"){
			if($type == 0){
				$m = 'تم إلغاء الطلب';
			}elseif($type == 1){
				$m = 'تم تسليم الطلب بنجاح';
			}else{
				$m = 'تمت العملية بنجاح';
			}
			$msg = array(
				0  => $m,
				1  => 'كل الحقول مطلوبه',
				2  => 'قيمة نوع العملية يجب ان تنحصر بين 0 و 1',
				3  => 'فشلت العمليه'
			);
		}else{
			if($type == 0){
				$m = 'Order canceled successfully';
			}elseif($type == 1){
				$m = 'Order delivered successfully';
			}else{
				$m = 'Process done successfully';
			}
			$msg = array(
				0  => $m,
				1  => 'All fields are required',
				2  => 'type value must be between 0 and 1',
				3  => 'Process failed',
			);
		}

		$messages = array(
			'required' => 1,
			'in'	   => 2
		);

		$validator = Validator::make($request->all(), [
			'order_id'    => 'required',
			'delivery_id' => 'required',
			'type'		  => 'required|in:0,1'
		], $messages);

		if($validator->fails()){
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => $error, 'msg' => $msg[$error]]);
		}else{
			if($type == 0){
				//cancel order
				DB::table("orders_headers")->where('order_id', $request->input('order_id'))->update(["status_id" => 2]);
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			}else{
				//get order payment type
				$data           = DB::table("orders_headers")->where('order_id', $request->input('order_id'))->select('payment_type', 'delivery_price', 'app_value' , 'delivery_app_value')->first();
				$payment_type   = $data->payment_type;
				$delivery_price = $data->delivery_price;
				$app_value      = $data->app_value;
				$delivery_app_value      = $data->delivery_app_value;
				$net 			= ($delivery_price - $app_value);
				$delivery_id    = $request->input('delivery_id');
				$order_id       = $request->input('order_id');
				try {
					DB::transaction(function() use($app_value, $net, $delivery_id, $order_id, $payment_type, $delivery_app_value, $delivery_price){
						DB::table('orders_headers')->where('order_id', $order_id)->update(['status_id' => 4]);
						if($payment_type != 1){  // try to change it to == 2 and test it 
							DB::table("balances")->where("actor_id", $delivery_id)
												 ->where('type', 'delivery')
												 ->update([ 'current_balance' => DB::raw('current_balance + '. $delivery_price) ]);
						}else{
							DB::table("balances")->where("actor_id", $delivery_id)
												 ->where('type', 'delivery')
												 ->update([ 'due_balance' => DB::raw('due_balance + '. $delivery_app_value) ]);
						}
					});

					if($lang == "ar"){
						if($type == 1){
							$title   = "توصيل الطلب";
							$message = "تم توصيل طلبك بنجاح";
							$type    = 'evaluate';
						}else{
							$title   = "إلغاء طلب";
							$message = "تم إلغاء طلبك من قبل الموصل";
							$type    = 'order';
						}
					}else{
						if($type == 1){
							$title   = "Order delivered";
							$message = "Your order has been delivered successfully";
							$type    = 'delivery_evaluate';
						}else{
							$title   = "Order canceled";
							$message = "Your order has been canceled by delivery";
							$type    = 'order';
						}
					}
					//get user and provider tokens
					$getTokens = DB::table('orders_headers')
								   ->where('orders_headers.order_id', $order_id)
								   ->join('users', 'orders_headers.user_id', '=', 'users.user_id')
								   ->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
								   ->select('providers.device_reg_id AS provider_token', 'users.device_reg_id AS user_token')
								   ->first();
					$notif_data = array();
					$notif_data['title']      = $title;
				    $notif_data['message']    = $message;
				    $notif_data['order_id']   = $order_id;
				    $notif_data['notif_type'] = $type;
					$push_notif = $this->singleSend($getTokens->user_token, $notif_data, $this->user_key);
					$notif_data['notif_type'] = 'order';
					$push_notif = $this->singleSend($getTokens->provider_token, $notif_data, $this->provider_key);
					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
				} catch (Exception $e) {
					return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
				}
			}
		}
	}

	public function setLocTracker(Request $request){
		DB::table('orders_headers')->where('order_id', $request->input('order_id'))
								   ->update([
								   		'track_long' => $request->input('lng'),
								   		'track_lat'  => $request->input('lat')
								   	]);
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '']);
	}

	public function getLocTracker(Request $request){
		$get = DB::table('orders_headers')->where('order_id', $request->input('order_id'))
										  ->select('track_long', 'track_lat')
										  ->first();
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'locations' => $get]);
	}
}