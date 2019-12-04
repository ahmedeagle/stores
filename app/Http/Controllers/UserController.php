<?php

namespace App\Http\Controllers;

/**
 * Class UserController.
 * it is a class to manage all users functionalities
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController as NotifyC;
use App\Http\Controllers\PushNotificationController as Push;
use App\Product;
use App\Providers;
use App\Traits\Helpers;
use App\Traits\MainImage;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Mail;
use Validator;
use Storage;

class UserController extends Controller
{

	use MainImage;
	use Helpers;

	public function __construct(Request $request)
	{

	}

	//method to prevent visiting any api link
	public function echoEmpty()
	{
		echo "";
	}

	// protected function saveImage($data, $image_ext, $path){

	//       if(!empty($data)){
	//                          $errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";

	//                     try{
	//                         $file_data =  $image_ext;
	//                         $file_name = 'img-'.str_random(25).'.jpg'; //generating unique file name;
	//                           \Storage::disk($path)->put($file_name,base64_decode($file_data));
	//                           return   $file_name;
	//                         }catch(Exception $e){

	//                             return response()->json(['status'=> false, 'errNum' => 30, 'msg' =>$errMsg]);
	//                         }
	//     }else{
	//         return "";
	//     }
	// }

	/**
	 * Registation for zad user application
	 * This function is to register zad user
	 * it works by receiving data from zad user mobile application (ANDROID & IOS)
	 * then store it in the database
	 */
	public function userSignUp(Request $request)
	{

		//get applicatoin language
		$lang = $request->input('lang');

		//set user status to be not activated
		$status = 0;

		/**
		 * preparing validation error messages
		 * it will be anumber
		 * that number will be a pointer to an error string from errors messeges array
		 * select error messages upon the language of the application
		 */
		$messages = array(
			'full_name.required' => 1,
			'full_name.min' => 2,
			'email.required' => 3,
			'phone.required' => 4,
			'phone.numeric' => 5,
			'country_code.required' => 22,
			'email' => 6,
			'longitude.required_with' => 7,
			'latitude.required_with' => 7,
			'password.required' => 8,
			'password.min' => 9,
			'phone.unique' => 13,
			'email.unique' => 14,
			'required_with' => 15,
			'invitation_code.exists' => 16,
			'city_id.required' => 17,
			'city_id.numeric' => 18,
			'activation_code.required' => 19,
			'reg_id.required' => 23,
			'country_id.required' => 24,
			'country_id.numeric' => 25,
			'password.confirmed' => 26,
			'country_id.exists' => 27,
			'city_id.exists' => 28,
			'password_confirmation.required' => 29,
			'regex' => 30,
			'image_ext.required' => 31,
		);

		// seting error messages array
		if ($lang == 'ar') {
			//error messages array for
			$messagesStr = array(
				1 => 'الإسم بالكامل  مطلوب',
				2 => 'الإسم بالكامل لا يجب ان يقل عن 3 حروف',
				3 => 'البريد الإلكترونى   مطلوب',
				4 => 'الجوال حقل مطلوب',
				5 => 'حقل الجوال يجب ان يكون ارقام فقط',
				6 => 'حقل الإميل يجب ان يكون فى شكل البريد الإلكترونى',
				7 => 'يجب تحديد مكانك على الخريطة',
				8 => 'حقل كلمة السر مطلوب',
				9 => 'حقل كلمة السر لا يجب ان يقل عن 8 حروف',
				10 => 'يجب ان تحدد نوع المستخدم',
				11 => 'نوع المستخدم يجب ان يكون مكون من رقم واحد فقط',
				12 => 'نوع المستخدم يجب ان يكون رقم',
				13 => 'رقم الجوال مستخدم من قبل',
				14 => 'البريد الإلكترونى مستخدم من قبل',
				15 => 'يجب ان تحدد إمتداد صورة البروفايل',
				16 => 'خطأ فى كود الدعوه',
				17 => 'رقم المدينه مطلوب',
				18 => 'رقم المدينه يجب ان يكون رقم',
				19 => 'كود التفعيل مطلوب',
				22 => 'يرجى إضافة كود الدوله',
				23 => 'رقم الجهاز مطلوب',
				24 => 'رقم الدوله مطلوب',
				25 => 'رقم الدوله يجب ان يكون رقم',
				26 => 'كلمتة المرور غير متطابقة ',
				27 => 'رقم الدولة غير موجود ',
				28 => 'قم المدينة غير موجود ',
				29 => 'لابد من تاكيد كلمة المرور ',
				30 => 'صيغة رقم الهاتف غير صحيحة لابد ان تبدا ب 5 او 05',
				31 => 'لابد من ادخال امتداد الصوره الشخصية ',
			);
			$city_col = "city_ar_name AS city_name";
		} else {

			$messagesStr = array(
				1 => 'Full Name is required',
				2 => 'Full Name must be more than 3 characters',
				3 => 'E-mail is required',
				4 => 'Phone is required',
				5 => 'Phone must be only digits',
				6 => 'E-mail must be in e-mail format',
				7 => 'Please locate your location on map',
				8 => 'Password is required',
				9 => 'Password must be more than 7 characters',
				10 => 'Please determine user type',
				11 => 'User type must be only 1 digit',
				12 => 'User type must be a digit',
				13 => 'Phone number is used before',
				14 => 'E-mail is used before',
				15 => 'You must determine profile picture extenstion',
				16 => 'Wrong invitation_code',
				17 => 'city_id is required',
				18 => 'city_id must be a number',
				19 => 'activation_code is required',
				22 => 'country code is required',
				23 => 'device register id is required',
				24 => 'country_id is required',
				25 => 'country_id must be a number',
				26 => 'password confirmation wrong',
				27 => 'country  doesn\'t exists',
				28 => 'city  doesn\'t exists',
				29 => 'password confirmation required',
				30 => 'phone number format invalid must start with 5 or 05',
				31 => 'image_ext is required',

			);
			$city_col = "city_en_name AS city_name";
		}

		$rules = [
			'full_name' => 'required|min:3',
			// 'email'           => 'required|email|unique:users',
			'phone' => array('required', 'unique:users,phone', 'regex:/^(05|5)([0-9]{8})$/'),
			'country_code' => 'required',
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required',
			'city_id' => 'required|numeric|exists:city,city_id',
			'country_id' => 'required|numeric|exists:country,country_id',
			'longitude' => 'required_with:latitude',
			'latitude' => 'required_with:longitude',
			'reg_id' => 'required',
		];
		$image = "avatar_ic.png";

		if ($request->input('profile_pic')) {

			$rules['profile_pic'] = "required";
			$rules['image_ext'] = "required";

		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();

			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $messagesStr[$error]]);
		}
		//here we can set posted data sent from the mobile
		$fullName = $request->input('full_name');
		//$email         = $request->input('email');
		$phone = $request->input('phone');
		$password = $request->input('password');
		$image_ext = $request->input('image_ext');
		$longitude = $request->input('longitude');
		$latitude = $request->input('latitude');
		//    $invitation    = $request->input('invitation_code');
		$city = $request->input('city_id');
		$country = $request->input('country_id');
		$country_code = $this->checkCountryCodeFormate($request->input('country_code'));
		$device_reg_id = $request->input('reg_id');

		if (empty($latitude)) {
			$latitude = "";
		}
		if (empty($longitude)) {
			$longitude = "";
		}

		$profile_pic = "avatar_ic.png";
		if ($request->input('profile_pic') && !empty($request->profile_pic)) {

			//save new image   64 encoded
			// $image = $this->saveImage( $request -> profile_pic,'jpg', 'users');
			// $image = $this->saveImage(time(), $request->profile_pic, 'users/', ['jpeg', 'png', 'jpg', 'gif']);
			$image = $this->saveImage('userProfileImages/', $request->profile_pic);

			if ($image == "") {
				if ($lang == "ar") {
					$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
				} else {
					$errMsg = "Failed to upload image, please try again later";
				}

				return response()->json(['status' => false, 'errNum' => 30, 'msg' => $errMsg]);
			} else {
				$profile_pic = $image;
			}

		}

		// send activation code to provider

		$code = $this->generate_random_number(4);

		$token = $this->getRandomString(128);

		$activation_code = json_encode([
			'code' => $code,
			'expiry' => Carbon::now()->addDays(1)->timestamp,
		]);

		$message = (App()->getLocale() == "en") ?
			"Your Activation Code is :- " . $code :
			"رقم الدخول الخاص بك هو :- " . $code;

		//users model object
		$user = new User();

		//setting data to insert it
		$user->full_name = $fullName;
		//$user->email           = $email;
		$user->phone = $phone;
		$user->country_code = $country_code;
		$user->password = md5($password);
		$user->profile_pic = $image;
		// $user->type            = $type;
		$user->status = $status;
		$user->longitude = $longitude;
		$user->latitude = $latitude;
		$user->city_id = $city;
		$user->country_id = $country;
		$user->activation_code = $activation_code;
		$user->token = $token;
		$user->device_reg_id = $device_reg_id;

		// add app lang to users
		$user->lang = $request->input('lang') ? $request->input('lang') : 'ar';

		if ($request->input('invitationCode') && !empty($request->input('invitationCode'))) {

			// $user->invitation_code = $request->input('invitationCode');

			// owner of invitation code to add new balance

			$referalUser = DB::table('users')->where('invitation_code', $request->input('invitationCode'))->first();

			if ($referalUser) {

				$settings = DB::table('app_settings')->first();

				if ($settings) {
					$inviter_points = $settings->inviter_points;
					$invited_points = $settings->invited_points;
				} else {
					$inviter_points = 0;
					$invited_points = 0;
				}

				DB::table('balances')->where('actor_id', $referalUser->user_id)->where('type', 'user')->update(['current_balance', DB::raw('current_balance + ' . $inviter_points)]);

			}

		}

		//save user
		$userSave = $user->save();

		if ($userSave) {

			if ($lang == "ar") {
				$successMsg = "تم التسجيل بنجاح";
			} else {
				$successMsg = "Signed up successfully";
			}

			//initailize account balance
			DB::table('balances')->insert(['actor_id' => $user->id, 'type' => 'user', 'current_balance' => 0, 'due_balance' => 0]);

			//intialize settings

			$inputs['new_order'] = 0;
			$inputs['cancelled_order'] = 0;
			$inputs['offer_request'] = 0;
			$inputs['admin_notify'] = 1;
			$inputs['ticket_notify'] = 1;
			$inputs['order_delay'] = 0;
			$inputs['recieve_orders'] = 0;
			$inputs['order_status_user'] = 1;
			$inputs['actor_id'] = $user->id;
			$inputs['type'] = 'users';

			DB::table('notification_settings')->insert($inputs);

			// send phone activation code
			$res = (new SmsController())->send($message, $user->phone);

			$userData = $this->getUserData($user->id, $lang);
			// return json_encode($response_array);
			return response()->json(['status' => true, 'errNum' => 0, 'user' => $userData, 'msg' => $successMsg]);
		} else {
			if ($lang == "ar") {
				$errMsg = "فشلت العملية";
			} else {
				$errMsg = "Proccess failed";
			}
			return response()->json(['status' => false, 'errNum' => 21, 'user' => [], 'msg' => $errMsg]);
		}

	}

	public function activateUserAccount(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم التفعيل',
				1 => 'كود غير صحيح ',
				2 => 'لابد من  ادخال الكود ',
				3 => 'لابد من توكن المستخدم ',
				4 => 'فشل التفعيل من فضلك حاول لاقحا',
				5 => 'كود تفعيل غير صحيح ',
			);
		} else {
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
			'code.required' => 2,
			'access_token.required' => 3,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
			'code' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user_id = $this->get_id($request, 'users', 'user_id');
		if ($user_id == 0) {

			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

		$user = User::where('user_id', $user_id);
		$activate_phone_hash = $user->first()->activation_code;
		$code = json_decode($activate_phone_hash)->code;

		if ($code != $request->code) {
			return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		}

		$data['status'] = 1;
		$data['activation_code'] = null;

		$user->update($data);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

	}

	public function resendActivationCode(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم  ارسال الكود بنجاح ',
				1 => 'لابد من توكن المستخدم ',
				2 => 'فشل من فضلك حاول مجددا ',
			);
		} else {
			$msg = array(
				0 => 'code sent successfully',
				1 => 'access_token required',
				2 => 'failed try again later',
			);
		}

		$messages = array(
			'access_token.required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$data = [];

		$user_id = $this->get_id($request, 'users', 'user_id');
		if ($user_id == 0) {

			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		$user = User::where('user_id', $user_id);

		$code = $this->generate_random_number(4);

		$data['activation_code'] = json_encode([
			'code' => $code,
			'expiry' => Carbon::now()->addDays(1)->timestamp,
		]);

		$user->update($data);

		$message = (App()->getLocale() == "en") ?
			"Your Activation Code is :- " . $code :
			"رقم الدخول الخاص بك هو :- " . $code;

		$res = (new SmsController())->send($message, $user->first()->phone);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

	}

	public function userLogin(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم الدخول',
				1 => 'رقم التليفون مطلوب',
				2 => 'تاكد من رقم التليفون مع إضافة كود الدوله',
				3 => 'كلمة السر مطلوبه',
				4 => 'خطأ فى البيانات',
				5 => 'لم يتم تفعيل الحساب بعد',
				6 => 'رقم الجهاز مطلوب',
			);
			$city_col = "city.city_ar_name AS city_name";
		} else {
			$msg = array(
				0 => 'Logined successfully',
				1 => 'Phone is required',
				2 => 'Wrong phone number ',
				3 => 'Password is required',
				4 => 'Faild To authentication',
				5 => 'You need to activate your account',
				6 => 'device nubmer (reg_id) is required',
			);
			$city_col = "city.city_en_name AS city_name";
		}
		$messages = array(

			'phone.required' => 1,
			'password.required' => 3,
			'reg_id.required' => 6,

		);
		$validator = Validator::make($request->all(), [
			'phone' => 'required',
			'password' => 'required',
			'reg_id' => 'required',
		], $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		} else {
			$user = new User();
			$getUser = $user->where('users.password', md5($request->input('password')))
				->where(function ($q) use ($request) {
					$q->where('users.phone', $request->input('phone'))
						->orWhere(DB::raw('CONCAT(users.country_code,users.phone)'), $request->input('phone'));
				})
				->join('city', 'users.city_id', 'city.city_id')
				->select('users.*', $city_col)
				->first();
			if ($getUser != null && !empty($getUser) && $getUser->count()) {
				$user->where('user_id', $getUser->user_id)->update(['device_reg_id' => $request->input('reg_id')]);
				$userData = array(
					'user_id' => $getUser->user_id,
					'full_name' => $getUser->full_name,
					'profile_pic' => env('APP_URL') . '/public/userProfileImages/' . $getUser->profile_pic,
					'status' => $getUser->status,
					'phone' => $getUser->phone,
					'country_code' => $getUser->country_code,
					//'email'           => $getUser->email,
					'longitude' => $getUser->longitude,
					'latitude' => $getUser->latitude,
					'city_id' => $getUser->city_id,
					'country_id' => $getUser->country_id,
					'city_name' => $getUser->city_name,
					'access_token' => $getUser->token,

				);
				if ($getUser->status == 0 || $getUser->status == "0") {

					// ############## send activation mobile code ########################################
					$data = [];
					$code = $this->generate_random_number(4);
					$data['activation_code'] = json_encode([
						'code' => $code,
						'expiry' => Carbon::now()->addDays(1)->timestamp,
					]);
					User::where('user_id', $getUser->user_id)->update($data);
					$message = (App()->getLocale() == "en") ?
						"Your Activation Code is :- " . $code :
						"رقم الدخول الخاص بك هو :- " . $code;
					$res = (new SmsController())->send($message, $getUser->phone);
					// ############## send activation mobile code ########################################

					return response()->json(['status' => false, 'errNum' => 5, 'user' => $userData, 'msg' => $msg[5]]);
				}

				return response()->json(['status' => true, 'errNum' => 0, 'user' => $userData, 'msg' => $msg[0]]);
			} else {
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
	}

	public function forgetPassword(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				1 => 'رقم الهاتف مطلوب ',
				2 => 'رقم هاتف غير صحيح ',
				3 => 'رقم الهاتف غير موجود ',
				4 => 'تم ارسال كود تفعيل الي هاتفك ',
				5 => 'رقم الهاتف غير مفعل ',

			);

		} else {
			$msg = array(
				1 => 'Phone is required',
				2 => 'Wrong phone number',
				3 => 'phone doesn\'t exists',
				4 => 'activation code sent successfully',
				5 => 'phone not active',
			);

		}
		$rules = [
			"phone" => "required|numeric|exists:users,phone",
		];

		$messages = [
			"required" => 1,
			"numeric" => 2,
			"exists" => 3,
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		//select proser vider base on his/her phone number if exists
		$userData = DB::table("users")->where("phone", $request->input("phone"))->select("user_id")->first();

		$user = User::where('user_id', $userData->user_id);

		$code = $this->generate_random_number(4);

		$message = (App()->getLocale() == "en") ?
			"Your Activation Code is :- " . $code :
			$code . "رقم الدخول الخاص بك هو :- ";

		$activation_code = json_encode([
			'code' => $code,
			'expiry' => Carbon::now()->addDays(1)->timestamp,
		]);

		$user->update([
			'activation_code' => $activation_code,
		]);

		(new SmsController())->send($message, $user->first()->phone);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4], "access_token" => $user->first()->token]);

	}

	public function resetPassword(Request $request)
	{

		$lang = $request->input('lang');

		$rules = [
			"password" => "required|min:8|confirmed",
			"access_token" => "required",
		];

		$messages = [
			"password.required" => 1,
			"password.required" => 1,
			'password.min' => 2,
			'password.confirmed' => 3,
			'access_token.required' => 5,
		];

		if ($lang == "ar") {
			$msg = array(
				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور  8 احرف ع الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
				4 => 'تم تغيير كلمة  المرور بنجاح ',
				5 => 'توكن المستخدم غير موجود',

			);

		} else {
			$msg = array(
				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
				4 => 'password successfully updated',
				5 => 'user token required',
			);

		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user = User::where('user_id', $this->get_id($request, 'users', 'user_id'))
			->update([

				'password' => md5($request->input('password')),
				'activation_code' => null,
			]);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);
	}

	public function updatePassword(Request $request)
	{

		$lang = $request->input('lang');

		$rules = [
			"password" => "required|min:8|confirmed",
			"access_token" => "required",
			"old_password" => "required",
		];

		$messages = [
			"required" => 1,
			'password.min' => 2,
			'password.confirmed' => 3,
			'old_password.required' => 5,
		];

		if ($lang == "ar") {
			$msg = array(
				1 => 'لابد من ادخال كلمة المرور ',
				2 => 'كلمه المرور 8 أحرف على الاقل ',
				3 => 'كلمة المرور غير متطابقه ',
				4 => 'تم تغيير كلمة المرور بنجاح ',
				5 => 'لابد من ادخال كلمة المرور الحالية',
				6 => 'كلمة المرور الحالية غير صحيحة',
			);

		} else {
			$msg = array(
				1 => 'password field required',
				2 => 'password minimum characters is 8',
				3 => 'password not confirmed',
				4 => 'password successfully updated',
				5 => 'Current password is required',
				6 => 'Current password is invalid',
			);

		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		//check for old password
		$check = User::where('token', $request->access_token)
			->where('password', md5($request->old_password))
			->first();

		if ($check) {

			User::where('user_id', $this->get_id($request, 'users', 'user_id'))
				->update([
					'password' => md5($request->input('password')),
//					'activate_phone_hash' => null,
				]);
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[4]]);

		} else {
			return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		}

	}

	protected function getUserData($user, $lang, $action = "get")
	{
		if ($lang == "ar") {
			$city_col = "city.city_ar_name AS city_name";
		} else {
			$city_col = "city.city_en_name AS city_name";
		}

		return User::where("user_id", $user)
			->join('city', 'users.city_id', '=', 'city.city_id')
			->select('users.user_id',
				'users.full_name',
				'users.profile_pic',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/userProfileImages/',users.profile_pic) AS profile_pic"),
				'users.status',
				'users.phone',
				'users.country_code',
				//    'users.email',
				'users.longitude',
				'users.latitude',
				'users.city_id',
				'users.country_id',
				'users.token AS access_token',
				$city_col)
			->first();
	}

	public function getProfileData(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$country_col = "country_ar_name AS country_name";
			$city_col = "city_ar_name AS city_name";
			$msg = array(
				0 => '',
				1 => 'توكن المستخدم مطلوب',
				2 => 'لا يوجد بيانات',
				3 => 'المستخدم غير موجود ',
			);
		} else {
			$country_col = "country_en_name AS country_name";
			$city_col = "city_en_name AS city_name";
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'There is no data',
				3 => 'user not found ',
			);
		}

		$messages = array(
			'required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = $this->get_id($request, 'users', 'user_id');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

		$userData = User::where('user_id', $userId)
			->select("user_id", "full_name AS user_name", 'phone', 'country_code', 'email',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/userProfileImages/',users.profile_pic) AS profile_pic"),
				'country_id',
				'city_id',
				'status',
				'created_at'
			)
			->first();

		$userCountry = $userData->country_id;
		$userCity = $userData->city_id;

		$countries = DB::table('country')->where('publish', 1)->select('country_id', $country_col, DB::raw('IF(country_id = ' . $userCountry . ', true, false) AS chosen'), 'country_code')->get();

		$cities = DB::table('city')->select('city_id', $city_col, DB::raw('IF(city_id = ' . $userCity . ', 1, 0) AS chosen'))->get();

		return response()->json([
			'status' => true,
			'errNum' => 0,
			'msg' => $msg[0],
			'user' => $userData,
			'countries' => $countries,
			'cities' => $cities,
		]);

	}

	public function UpdateProfile(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم تعديل البيانات بنجاح',
				1 => 'كل الحقول مطلوبه',
				2 => 'الدولة غير موجودة ',
				3 => ' المدينة  غير موجودة',
				4 => 'المستخدم غير موجود ',
				5 => 'فشلت العمليه من فضلاك حاول لاحقا',
				6 => ' رقم الجوال لابد ان يكون ارقام ',
				7 => 'صوره الملف الشخصي غير  صالحة ',
				8 => 'صيغه الهاتف غير صحيحة ',
				9 => 'رقم الهاتف موجود من قبل من فضلك ادخل رقم اخر ',

			);

		} else {
			$msg = array(
				0 => 'Updated successfully',
				1 => 'All fields are required',
				2 => 'Country doesn\'t exists',
				3 => 'country doesn\'t exists',
				4 => 'user Not Found ',
				5 => 'Failed to update, please try again later',
				6 => ' phone number must be numeric',
				7 => 'profile picture not valid',
				8 => 'phone number format invalid',
				9 => 'phone number used before',

			);
		}

		$messages = array(

			'required' => 1,
			'country_id.exists' => 2,
			'city_id.exists' => 3,
			'phone.numeric' => 6,
			'mimes' => 7,
			'regex' => 8,
			'phone.unique' => 9,
		);

		$rules = [

			'access_token' => 'required',
			'full_name' => 'required',
			'country_id' => 'required|exists:country,country_id',
			'city_id' => 'required|exists:city,city_id',

		];

		$userId = $this->get_id($request, 'users', 'user_id');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
		}

		$user = DB::table("users")->where('user_id', $userId);

		$input = $request->only('full_name', 'phone', 'city_id', 'country_id');

		$input['country_code'] = $this->checkCountryCodeFormate($request->input('country_code'));

		if ($input['phone'] != $user->first()->phone) {

			$rules['phone'] = array('required', 'numeric', 'regex:/^(05|5)([0-9]{8})$/', 'unique:users,phone,' . $userId . ',user_id');
			$rules['country_code'] = "required";

		} else {

			$rules['phone'] = array('required', 'numeric', 'regex:/^(05|5)([0-9]{8})$/');
			$rules['country_code'] = "required";

		}

		if ($request->profile_pic) {

			$rules['profile_pic'] = "required";
			$rules['image_ext'] = "required";

		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		if ($input['phone'] != $user->first()->phone) {

			$code = $this->generate_random_number(4);

			$input['activation_code'] = json_encode([
				'code' => $code,
				'expiry' => Carbon::now()->addDays(1)->timestamp,
			]);

			$input['status'] = "0";

			$message = (App()->getLocale() == "en") ?
				"Your Activation Code is :- " . $code :
				$code . "رقم الدخول الخاص بك هو :- ";

			(new SmsController())->send($message, $user->first()->phone);

			$isPhoneChanged = true;
		} else {
			$isPhoneChanged = false;
		}

		if ($request->profile_pic && !empty($request->profile_pic)) {

			if ($user->first()->profile_pic != null && $user->first()->profile_pic != "") {

				//delete the previous image from storage
				if (Storage::disk('users')->exists($user->first()->profile_pic)) {

					Storage::disk('users')->delete($user->first()->profile_pic);

				}

				//save new image    64 encoded

				// $image = $this->saveImage( $request -> profile_pic,'jpg', 'users');
				// $image = $this->saveImage(time(), $request->profile_pic, 'users/', ['jpeg', 'png', 'jpg', 'gif']);
				$image = $this->saveImage('userProfileImages/', $request->profile_pic);

				if ($image == "") {
					if ($lang == "ar") {
						$errMsg = "فشل فى رفع الصورة حاول فى وقت  لاحق";
					} else {
						$errMsg = "Failed to upload image, please try again later";
					}

					return response()->json(['status' => false, 'errNum' => 15, 'msg' => $errMsg]);
				} else {

					$input['profile_pic'] = $image;
				}

			}

		}

		$input['status'] = $isPhoneChanged ? 0 : 1;

		$user->update($input);

		$getUser = $user->first();

		$userData = array(
			'id' => $getUser->user_id,
			'full_name' => $getUser->full_name,
			'phone' => $getUser->phone,
			'country_code' => $getUser->country_code,
			'access_token' => $getUser->token,
			'status' => $getUser->status,
			'country_id' => $getUser->country_id,
			'city_id' => $getUser->city_id,
			'status' => $getUser->status,
			'profile_pic' => env('APP_URL') . '/public/userProfileImages/' . $getUser->profile_pic,

			'created_at' => date('Y-m-d h:i:s', strtotime($getUser->created_at)),

		);

		//isPhoneChanged to notify mobile  app developers to redirect to activate phone number page

		return response()->json([

			'status' => true,
			'errNum' => 0,
			'msg' => $msg[0],
			'data' => $userData,
			'isPhoneChanged' => $isPhoneChanged,

		]);

	}

	public function mainCats(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {

			$cat_col = "categories.cat_ar_name AS cat_name";

		} else {

			$cat_col = "categories.cat_en_name AS cat_name";
		}

		$maincategory = DB::table('categories')
			->where('categories.publish', 1)
			->select(
				'categories.cat_id',
				'categories.cat_img',
				$cat_col,
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/categoriesImages/',categories.cat_img) AS cat_image")
			)
			->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'maincat' => $maincategory]);

	}

	public function get_nearest_providers_inside_main_sub_categories(Request $request)
	{

		$lang = $request->input('lang');

		//0 filter by distance   //1 filter by rate   //2 filter by none
		if ($lang == "ar") {

			$name = 'ar';

			$msg = [
				1 => 'جميع الحقول مطلوبة ',
				2 => 'التصنيف غير موجود ',
				3 => 'تمت العملية بنجاح ',
				4 => 'لابد من ادخال النوع بين  0 , 1 ',
			];

		} else {

			$name = 'en';

			$msg = [
				1 => 'all fields required',
				2 => 'category id doesn\'t exists',
				3 => 'done successfully',
				4 => 'must select type from 0,1',
			];
		}

		$conditions = [];
		if ($request->has('all_stores')) {

			if ($request->all_stores == 1 or $request->all_stores == '1') {

				$rules = [
					"type" => "required|in:0,1",
				];
			}
		} else {

			$rules = [
				"cat_id" => "required|exists:categories,cat_id",
				"type" => "required|in:0,1",
			];

			$conditions['categories.cat_id'] = $request->input("cat_id");
		}

		$messages = [
			"required" => 1,
			"exists" => 2,
			"in" => 4,
		];

		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$type = $request->input("type");

		if (!empty($conditions)) {

			$pagianted_providers = DB::table("categories")
				->join("providers", "providers.category_id", "categories.cat_id")
				->where($conditions)
				->where("providers.publish", 1)
				->select(
					"providers.provider_id",
					"providers.category_id",
					"providers.store_name AS store_name",
					"providers.provider_rate",
					"providers.membership_id",
					"providers.latitude",
					"providers.longitude",
					"providers.token AS access_token",

					DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS provider_image"),
					DB::raw("CONCAT('" . env('APP_URL') . "','/public/categoriesImages/',categories.cat_img) AS cat_image")
				)
				->groupBy("providers.provider_id")
				->paginate(10);

		} else {

			$pagianted_providers = DB::table("categories")
				->join("providers", "providers.category_id", "categories.cat_id")
				->where("providers.publish", 1)
				->select(
					"providers.provider_id",
					"providers.category_id",
					"providers.store_name AS store_name",
					"providers.provider_rate",
					"providers.membership_id",
					"providers.latitude",
					"providers.longitude",
					"providers.token AS access_token",
					DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS provider_image"),
					DB::raw("CONCAT('" . env('APP_URL') . "','/public/categoriesImages/',categories.cat_img) AS cat_image")
				)
				->groupBy("providers.provider_id")
				->paginate(10);

		}

		(new HomeController())->filter_providers($request, $name, $pagianted_providers, $type);

		if ($type == 0) {
			// filter based on distance by nearest

			/*$providers = $pagianted_providers->sortBy(function ($item) {
				return $item->distance;
			})->values();*/


			$providers = $pagianted_providers->filter(function ($item) {
				$desiredDistance = 100;
				return $item->distance < $desiredDistance;
			})->values();

		} else {
			// filter by rate
			$providers = $pagianted_providers->sortByDesc(function ($item) {
				return $item->averageRate;
			})->values();
		}

		//used to make pagination from collection

		$providers = new LengthAwarePaginator(
			$providers,
			$pagianted_providers->total(),
			$pagianted_providers->perPage(),
			$request->input("page"),
			["path" => url()->current()]

		);

		//
		$categories = DB::table('categories')
			->where('publish', 1)
			->select(
				'cat_id',
				'cat_en_name',
				'cat_ar_name',
				'cat_img',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/categoriesImages/',categories.cat_img) AS cat_image")
			)
			->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3], "providers" => $providers, 'categories' => $categories]);
	}

	public function prepareProviderPage(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم جلب البيانات بنجاح ',
				1 => 'لابد من رقم المتجر ',
				2 => 'المتجر غير موجود ',
				3 => 'المستخدم غير موجود ',
				4 => 'توكن المستخدم مطلوب في حاله تم تمريرة ',
			);
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'provider_id doesn\'t exists',
				2 => 'provider not exists',
				3 => 'user not found ',
				4 => 'user access token required when  it pass',

			);
		}
		$messages = array(
			'provider_id.required' => 1,
			'provider_id.exists' => 2,
			'access_token.required' => 4,
		);
		$validator = Validator::make($request->all(), [
			'provider_id' => 'required|numeric|exists:providers,provider_id',

		], $messages);

		$providerId = $request->provider_id;

		$userId = 0; // return all products

		if ($request->has('access_token')) {

			$rules['access_token'] = "required";

			$userId = $this->get_id($request, 'users', 'user_id');

			if ($userId == 0) {
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$rates = DB::table('providers_rates')
			->where('providers_rates.provider_id', $providerId)
			->select(
				DB::raw("IFNULL(COUNT(providers_rates.id),0)  AS number_of_rates"),
				DB::raw("IFNULL(SUM(providers_rates.rates),0) AS sum_of_rates")
			)
			->first();

		$numberOfRates = $rates->number_of_rates;
		$sumRate = $rates->sum_of_rates;
		if ($numberOfRates != 0 && $numberOfRates != null) {
			$totalAverage = $sumRate / $numberOfRates;
		} else {
			$totalAverage = 0;
		}

		//get provider data
		$provider = Providers::where('provider_id', $providerId)
			->leftJoin('categories', 'providers.category_id', 'categories.cat_id')
			->select('provider_id',
				'store_name',
				'phone',
				'membership_id',
				'delivery_price',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS profile_pic"),
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/categoriesImages/',categories.cat_img) AS cat_img"),
				DB::raw(" '" . $totalAverage . "' AS provider_rate")

			)
			->first();

		//get provider categories
		$providerCategories = DB::table('categories_stores')
			->where('categories_stores.provider_id', $providerId)
			->select('categories_stores.id AS cat_id', 'categories_stores.store_cat_ar_name', 'categories_stores.store_cat_en_name'

			)
			->get();

		$catId = $request->input('cat_id');

		//get current provider and category products
		if ($catId != 0) {
			$products = Product::where('products.provider_id', $providerId)
				->where('products.category_id', $catId)
				->where('products.publish', 1)
				->select('products.id', 'products.title', 'products.price',
					'products.likes_count', 'products.product_rate',
					DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = ' . $userId . ' AND product_likes.product_id = products.id) > 0, 1, 0) as isFavorit'));

			$numOfProducts = $products->count();
			$products = $products->paginate(10);

			$provider->numOfProducts = $numOfProducts;
			foreach ($products as $product) {
				$data = DB::table("product_images")
					->where("product_images.product_id", $product->id)
					->select(
						DB::raw("CONCAT('" . env('APP_URL') . "','/public/products/',product_images.image) AS product_image")

					)
					->first();
				if ($data) {
					$product->product_image = $data->product_image;
				} else {
					$product->product_image = "";
				}

			}

		} else {
			$products = Product::where('products.provider_id', $providerId)
				->where('products.publish', 1)
				->select('products.id', 'products.title',
					'products.price',
					'products.likes_count', 'products.product_rate',
					DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = ' . $userId . ' AND product_likes.product_id = products.id) > 0, 1, 0) as isFavorit'));

			$numOfProducts = $products->count();
			$products = $products->paginate(10);

			$provider->numOfProducts = $numOfProducts;

			foreach ($products as $product) {
				$data = DB::table("product_images")
					->where("product_images.product_id", $product->id)
					->select(
						DB::raw("CONCAT('" . env('APP_URL') . "','/public/products/',product_images.image) AS product_image")

					)
					->first();
				if ($data) {
					$product->product_image = $data->product_image;
				} else {
					$product->product_image = "";
				}

			}

		}

		return response()->json(['status' => true, 'errNum' => 0, 'provider' => $provider, 'providerCategories' => $providerCategories, 'products' => $products, 'msg' => $msg[0]]);

	}

	//method to get  product details with product id
	public function getProductDetails(Request $request)
	{

		$productId = $request->input('product_id');
		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم جلب البيانات بنجاح ',
				1 => 'لابد من رقم المنتج  ',
				2 => ' المنتج  غير موجود ',
				3 => 'المستخدم غير موجود ',
			);

			$cat_col = "store_cat_ar_name AS cat_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'product id required ',
				2 => 'product_id doesn\'t exists',
				3 => 'user not found ',

			);

			$cat_col = "store_cat_en_name AS cat_name";
		}

		$messages = array(
			'product_id.required' => 1,
			'product_id.exists' => 2,
			'access_token.required' => 3,
		);

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|numeric|exists:products,id',

		], $messages);

		$userId = 0; // return all products

		if ($request->has('access_token')) {

			$rules['access_token'] = "required";

			$userId = $this->get_id($request, 'users', 'user_id');

			if ($userId == 0) {
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		}

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		//get product details
		$productDetails = Product::where('products.id', $productId)
			->join('providers', 'products.provider_id', '=', 'providers.provider_id')
			->join('categories_stores', 'products.category_id', '=', 'categories_stores.id')
			->leftJoin('categories', 'categories.cat_id', '=', 'products.category_id')
			->select('products.id AS product_id', 'products.title', 'products.description', 'products.price', 'products.quantity',
				'products.likes_count', 'providers.store_name AS store_name', 'providers.membership_id', 'providers.provider_id AS provider_id', $cat_col,

				DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = ' . $userId . ' AND product_likes.product_id = ' . $productId . ') > 0, 1, 0) as isFavorit'),

				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS profile_pic"),
				'providers.latitude',
				'providers.longitude'
			)
			->first();

		$rates = DB::table('products_rates')
			->where('products_rates.product_id', $productId)
			->select(
				DB::raw("COUNT(products_rates.id) AS number_of_rates"),
				DB::raw("SUM(products_rates.rates) AS sum_of_rates")
			)
			->first();

		$numberOfRates = $rates->number_of_rates;
		$sumRate = $rates->sum_of_rates;
		if ($numberOfRates != 0 && $numberOfRates != null) {
			$totalAverage = ceil($sumRate / $numberOfRates);
		} else {
			$totalAverage = 0;
		}

		// product average rat
		$productDetails->totalAverageRate = $totalAverage;

		$Prates = DB::table('providers_rates')
			->where('providers_rates.provider_id', $productDetails->provider_id)
			->select(
				DB::raw("IFNULL(COUNT(providers_rates.id),0)  AS number_of_rates"),
				DB::raw("IFNULL(SUM(providers_rates.rates),0) AS sum_of_rates")
			)
			->first();

		$numberOfPRates = $Prates->number_of_rates;
		$sumRate = $rates->sum_of_rates;
		if ($numberOfPRates != 0 && $numberOfPRates != null) {
			$PtotalAverage = $sumRate / $numberOfPRates;
		} else {
			$PtotalAverage = 0;
		}

		$productDetails->ProvidertotalAverageRate = $PtotalAverage;

		$data = DB::table("product_images")
			->where("product_images.product_id", $productDetails->product_id)
			->select(
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/products/',product_images.image) AS product_image")
			)
			->first();
		if ($data) {
			$productDetails->product_image = $data->product_image;
		} else {
			$productDetails->product_image = "";
		}

		//get product images
		$productImages = DB::table('product_images')
			->where('products.id', $productId)
			->join('products', 'products.id', '=', 'product_images.product_id')
			->select('product_images.id',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/products/',product_images.image) AS product_image")
			)
			->get();

		//get product comments
		$comments = DB::table('product_comments')
			->where('products.id', $productId)
			->join('products', 'product_comments.product_id', '=', 'products.id')
			->join('users', 'product_comments.user_id', '=', 'users.user_id')
			->select('users.full_name',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/userProfileImages/',users.profile_pic) AS user_profile_pic"),
				'users.user_id', 'product_comments.comment', 'product_comments.id', 'product_comments.product_id')
			->get();
		$count_comment = $comments->count();

		//get prodcut options

		$options = DB::table('product_options')->where('product_id', $productId)->select('id', 'name', 'price')->get();
		//get product sizes
		$sizes = DB::table('product_sizes')->where('product_id', $productId)->select('id', 'name', 'price')->get();
		//get product colors
		$colors = DB::table('product_colors')->where('product_id', $productId)->select('id', 'name', 'price')->get();

		//get user rate

		if (isset($comments) && $comments->count() > 0) {

			foreach ($comments as $index => $comment) {

				$productRate = DB::table('products_rates')->where('product_id', $comment->product_id)->where('user_id', $comment->user_id)->select('rates')->first();

				if ($productRate) {
					$comment->userRate = $productRate->rates;
				} else {
					$comment->userRate = 0;
				}

			}
		}

		return response()->json(['status' => true, 'errNum' => 0, 'product' => $productDetails, 'productImages' => $productImages, 'comments' => $comments, 'comments_count' => $count_comment, 'options' => $options, 'sizes' => $sizes, 'colors' => $colors, 'msg' => $msg[0]]);

	}

	public function like_product(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'تم اضافه المنتج للمفضله بنجاح ',
				1 => 'كل الحقول مطلوبه',
				2 => 'كل الحقول يجب ان تكون ارقام',
				3 => 'النوع يجب ان يكون إما 1 او 2',
				4 => 'رفقم المستخدم خطأ',
				5 => 'رقم المنتج خطأ',
				6 => 'حقل المفضله مطلوب ',
				7 => 'فشلت العملية من فضلك حاول فى وقت لاحق',
				8 => 'لابد من تسجيل الدخول اولا ',
				9 => 'المستخدم غير موجود ',
				10 => 'تمت الاضافه الي المفضله من قبل ',
				11 => 'تم الحذف من المفضله من قبل ',
				12 => 'تم حذف المنتج من المفضله ',

			);
		} else {
			$msg = array(
				0 => 'Product Add To Favourit successfully',
				1 => 'user access_token required',
				2 => 'product id required',
				3 => 'product doesn\'t exists',
				4 => 'product id must be numeric',
				5 => 'Type must be 0 to dislike or 1 to like',
				6 => 'Like Field requires',
				7 => 'Process failed, please try again later',
				8 => 'Must be Logined first',
				9 => 'User not found',
				10 => 'You like this product before',
				11 => 'You dislike this product before',
				12 => 'Product remove from favourit successfully',

			);
		}

		$messages = array(
			'access_token.required' => 1,
			'product_id.required' => 2,
			'product_id.exists' => 3,
			'product_id.numeric' => 4,
			'like.in' => 5,
			'like.required' => 6,

		);

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|numeric|exists:products,id',
			'like' => 'required|in:0,1',
		], $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = 0; // return all products

		if ($request->has('access_token')) {

			$rules['access_token'] = "required";

			$userId = $this->get_id($request, 'users', 'user_id');

			if ($userId == 0) {
				return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
			}
		}

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
		}

		$likeBefore = $this->userLikeProductBefore($userId, $request->input('product_id'));

		try {

			$data['like'] = $request->input('like');
			$data['product'] = $request->input('product_id');
			$data['user'] = $userId;

			if ($data['like'] == 1 || $data['like'] == "1") {

				if ($likeBefore) {

					return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
				} else {

					//insert data
					DB::table('product_likes')->insert([
						'product_id' => $data['product'],
						'user_id' => $data['user'],
					]);

					//update product table
					Product::where('id', $data['product'])->increment('likes_count', 1);

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

				}

			} else {

				if (!$likeBefore) {

					return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);
				} else {

					//Dislike
					DB::table('product_likes')->where('product_id', $data['product'])->delete();

					//update product table
					Product::where('id', $data['product'])->where('likes_count', '>', 0)->decrement('likes_count', 1);

					return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[12]]);
				}

			}

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		}

	}

	public function userLikeProductBefore($userId, $product_id)
	{

		$status = DB::table('product_likes')->where(['user_id' => $userId, 'product_id' => $product_id])->first();

		if ($status) {

			return true;
		}

		return false;
	}

	public function getUserFavorites(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'رقم المستخدم مطلوب',
				2 => 'لا يوجد بيانات بعد',
				3 => 'هذا المستخدم غير موجود ',
			);
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'access_token is required',
				2 => 'There is no data yet!!',
				3 => 'the user doesn\'t exists',
			);
		}

		$messages = array(
			'access_token.required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',

		], $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = 0; // return all products

		$userId = $this->get_id($request, 'users', 'user_id');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

		$data = DB::table('product_likes')->where('product_likes.user_id', $userId)
			->join('products', 'product_likes.product_id', '=', 'products.id')
			->join('providers', 'products.provider_id', '=', 'providers.provider_id')
			->select('products.id AS product_id', 'products.title', 'products.likes_count', 'products.product_rate', 'providers.store_name AS full_name', 'providers.provider_id', 'products.price',
				'providers.membership_id',
				'products.category_id',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS store_pic"),

				DB::raw('IF ((SELECT count(id) FROM product_likes WHERE product_likes.user_id = ' . $userId . ' AND product_likes.product_id = products.id) > 0, 1, 0) as isFavorit')
			)
			->orderBy('product_likes.id', 'DESC')
			->paginate(10);

		if (isset($data) && $data->count() > 0) {

			foreach ($data as $key => $product) {

				$images = DB::table("product_images")
					->where("product_images.product_id", $product->product_id)
					->select(
						DB::raw("CONCAT('" . env('APP_URL') . "','/public/products/',product_images.image) AS product_image")
					)
					->first();

				$rates = DB::table('providers_rates')
					->where('providers_rates.provider_id', $product->provider_id)
					->select(
						DB::raw("IFNULL(COUNT(providers_rates.id),0)  AS number_of_rates"),
						DB::raw("IFNULL(SUM(providers_rates.rates),0) AS sum_of_rates")
					)
					->first();

				$numberOfRates = $rates->number_of_rates;
				$sumRate = $rates->sum_of_rates;
				if ($numberOfRates != 0 && $numberOfRates != null) {
					$totalAverage = $sumRate / $numberOfRates;
				} else {
					$totalAverage = 0;
				}

				$product->provider_average_rate = $totalAverage;

				if ($images) {
					$product->product_image = $images->product_image;
				} else {
					$product->product_image = "";
				}

			}

			return response()->json(['status' => true, 'errNum' => 0, 'favourits' => $data, 'msg' => $msg[0]]);
		} else {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

	}

	//method to add comment  and rate product
	public function addComment(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'تم إضافة التعليق',
				1 => 'التعليق والمستخدم والوجبه حقول مطلوبه',
				2 => 'التقييم و المستخدم والوجبه يجب ان يكون ارقام',
				3 => 'التعليق لا يجب ان يقل عن 3 حروف',
				4 => 'المستخدم غير صحيح',
				5 => 'الوجبه غير صحيحة',
				6 => 'التقييم يجب ان يكون بين 1 و 5',
				7 => 'هناك خطأ ما من فضلك حاول فى وقت لاحق',
				8 => 'المستخدم غير موجود ',
				9 => 'تم تحديث التقييم بنجاح ',

			);
		} else {
			$msg = array(
				0 => 'Comment added',
				1 => 'Comment field required',
				2 => 'User access_token required',
				3 => 'Product_id field required',
				4 => 'Rate must be 1,2,3,4,5',
				5 => 'Comment must at lest 3 characters',
				6 => 'product doesn\'t exists',
				7 => 'There is something wrong, please try again later',
				8 => 'User does\'t exists',
				9 => 'Your rating updatted successfully',
			);
		}

		$messages = array(
			'comment.required' => 1,
			'access_token.required' => 2,
			'product_id.required' => 3,
			'rate.in' => 4,
			'comment.min' => 5,
			'product_id.exists' => 6,
		);

		$validator = Validator::make($request->all(), [
			'rate' => 'sometimes|nullable|in:1,2,3,4,5',
			'comment' => 'required|min:3',
			'access_token' => 'required',
			'product_id' => 'required|exists:products,id',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = $this->get_id($request, 'users', 'user_id');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
		}

		$productRate = 0;

		try {
			$data['user_id'] = $userId;
			$data['product_id'] = $request->input('product_id');
			$data['rate'] = $request->input('rate');
			$data['comment'] = $request->input('comment');
			$productRate = 0;
			DB::transaction(function () use ($data, &$productRate) {
				DB::table('product_comments')->insert([
					'product_id' => $data['product_id'],
					'comment' => $data['comment'],
					'user_id' => $data['user_id'],
				]);

				$updated = 0;

				if ($this->UserCommentBefore($data)) {

					DB::table('products_rates')->where(['product_id' => $data['product_id'], 'user_id' => $data['user_id']])->update([
						'product_id' => $data['product_id'],
						'rates' => $data['rate'] ? $data['rate'] : 0,
						'user_id' => $data['user_id'],
					]);

					$updated = 1;

				} else {

					DB::table('products_rates')->insert([
						'product_id' => $data['product_id'],
						'rates' => $data['rate'] ? $data['rate'] : 0,
						'user_id' => $data['user_id'],
					]);

					$updated = 0;

				}

				//get sum of rates and count
				$x = DB::table('products_rates')->where('product_id', $data['product_id'])
					->select(DB::raw('IFNULL(SUM(rates),0) as rateSum'), DB::raw('IFNULL(COUNT(id),0) AS rateCount'))
					->first();

				if ($x != null) {
					if ($x->rateCount != 0 && $x->rateCount != "0") {
						$productRate = $x->rateSum / $x->rateCount;
						$productRate = ceil($productRate);
					} else {
						$productRate = 0;
					}
				} else {
					$productRate = 0;
				}

				DB::table('products')->where('id', $data['product_id'])
					->update(['product_rate' => $productRate]);

			});

			if ($updated = 0) {

				return response()->json(['status' => true, 'errNum' => 0, 'productRate' => $productRate, 'msg' => $msg[0]]);

			}

			return response()->json(['status' => true, 'errNum' => 0, 'productRate' => $productRate, 'msg' => $msg[9]]);

		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		}

	}

	protected function UserCommentBefore($data)
	{

		$commented = DB::table('products_rates')->where(['product_id' => $data['product_id'], 'user_id' => $data['user_id']])->first();

		if ($commented) {
			return true;
		}

		return false;

	}

	//add address
	public function addAdress(Request $request)
	{
		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(

				0 => 'تم إضافة العنوان بنجاح',
				1 => 'خانة العنوان مطلوبه',
				2 => 'ةصف العنوان  مطلوب ',
				3 => 'رقم المستخدم مطلوب',
				4 => 'الاحداثي الاول للخريطه مطلوب ',
				5 => 'الاحداثي الثاني للخريطة مطلوب ',
				6 => 'فشلت العملية من فضلك حاول مجداا ',
				7 => 'المستخدم غير موجود ',
				8 => 'رقم  الهاتف مطلوب ',
				9 => ' رقم الهاتف لابد ان يكون ارقام ',
				10 => ' كود الدولة مطلوب ',
				11 => 'صسغة هاتف غير صحيحة ',
			);
		} else {
			$msg = array(
				0 => 'Address has been added successfully',
				1 => 'Address field  is required',
				2 => 'Address description field is required',
				3 => 'access_token is required',
				4 => 'latitude  is required ',
				5 => 'longitude is required',
				6 => 'Proccess failed please try again later',
				7 => 'User Not Found ',
				8 => 'phone number required',
				9 => 'phone number must be numeric',
				10 => 'country code required',
				11 => 'phone number format invalid',

			);
		}

		$messeges = array(
			'address.required' => 1,
			'description.required' => 2,
			'access_token.required' => 3,
			'latitude.required' => 4,
			'longitude.required' => 5,
			'phone.required' => 8,
			'phone.numeric' => 9,
			'country_code.required' => 10,
			'phone.regex' => 11,

		);
		$validator = Validator::make($request->all(), [
			'address' => 'required',
			'description' => 'required',
			'access_token' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'phone' => array('required', 'numeric', 'regex:/^(05|5)([0-9]{8})$/'),
			'country_code' => 'required',
		], $messeges);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = $this->get_id($request, 'users', 'user_id');
		$latitude = $request->input('latitude');
		$longitude = $request->input('longitude');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		}

		$id = DB::table('user_addresses')->insertGetId([
			'address' => $request->input('address'),
			'short_desc' => $request->input('description'),
			'user_id' => $userId,
			'longitude' => $longitude,
			'latitude' => $latitude,
			'country_code' => $this->checkCountryCodeFormate($request->input('country_code')),
			'phone' => $request->input('phone'),
		]);

		if ($id) {
			$addressDetail = array(
				'address' => $request->input('address'),
				'description' => $request->input('description'),
				'address_id' => $id,
				'country_code' => $this->checkCountryCodeFormate($request->input('country_code')),
				'phone' => $request->input('phone'),
				'longitude' => $longitude,
				'latitude' => $latitude,
			);
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addressDetail' => $addressDetail]);
		} else {
			return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);
		}

	}

	public function checkCountryCodeFormate($str)
	{

		if (mb_substr(trim($str), 0, 1) === '+') {
			return $str;
		}

		return '+' . $str;
	}

	//retieve user addresses
	public function getUserAddresses(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'رقم المستخدم مطلوب',
				2 => 'ألمستخدم غير موجود ',
				3 => 'لا يوجد بيانات بعد',
			);
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'access_token is required',
				2 => 'user not found',
				3 => 'There is no addresses yet',
			);
		}

		$messages = array(
			'required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$userId = $this->get_id($request, 'users', 'user_id');

		if ($userId == 0) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		//get user addresses
		$addresses = DB::table('user_addresses')->where('user_id', $userId)
			->select('address_id', 'user_id', 'short_desc AS short_address_desc', 'country_code', 'phone', 'address', 'longitude', 'latitude')->get();
		if (isset($addresses) && $addresses->count() > 0) {

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => $addresses]);
		} else {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

	}

	//delete user address
	public function deleteUserAddress(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == 'ar') {
			$msg = array(
				0 => 'تم مسح العنوان',
				1 => 'رقم العنوان مطلوب',
				2 => 'رقم العنوان يجب ان يكون رقم',
				3 => 'رقم العنوان غير موجود',
				4 => 'فشلت العملية من فضلك حاول فى وقت لاحق',
			);
		} else {
			$msg = array(
				0 => 'Deleted successfully',
				1 => 'address_id is required',
				2 => 'address_id must be a number',
				3 => 'address_id is not exist',
				4 => 'Proccess failed, please try again later',
			);
		}

		$messages = array(
			'required' => 1,
			'numeric' => 2,
			'exists' => 3,
		);

		$validator = Validator::make($request->all(), [
			'address_id' => 'required|numeric|exists:user_addresses,address_id',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}
		$check = DB::table('user_addresses')->where('address_id', $request->input('address_id'))->delete();
		if ($check) {
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} else {
			return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
		}

	}

	public function get_offers(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == 'ar') {
			$msg = array(
				0 => 'تم مسح العنوان',
				1 => 'رقم العنوان مطلوب',
				2 => 'رقم العنوان يجب ان يكون رقم',
				3 => 'رقم العنوان غير موجود',
				4 => 'فشلت العملية من فضلك حاول فى وقت لاحق',
				5 => 'المستخدم غير موجود',
			);
		} else {
			$msg = array(
				0 => 'Deleted successfully',
				1 => 'address_id is required',
				2 => 'address_id must be a number',
				3 => 'address_id is not exist',
				4 => 'Proccess failed, please try again later',
				5 => 'User Not exists',
			);
		}

		$messages = array(
			'required' => 1,
			'numeric' => 2,
			'exists' => 3,
		);

		$conditions = [];
		if ($request->access_token) {

			$userId = $this->get_id($request, 'users', 'user_id');

			if ($userId == 0) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

			$check = DB::table('users')->where('user_id', $userId)->first();

			if (!$check) {
				return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
			}

			$conditions['providers_offers.city_id'] = $check->city_id;

		}

		if ($conditions) {

			$offers = DB::table('providers')->join('providers_offers', 'providers.provider_id', 'providers_offers.provider_id')
				->whereExpire(0)
				->wherePaid('1')
				->where('providers_offers.publish', 1)
				->where('providers_offers.publish', 1)
				->where($conditions)
				->select(
					'providers_offers.id AS offer_id',
					'providers_offers.offer_title',
					DB::raw("CONCAT('" . url('/') . "','/offers/',providers_offers.photo) AS offer_photo"),
					'start_date',
					'end_date',
					'providers.provider_id'

				)
				->paginate(10);
		} else {

			$offers = DB::table('providers')->join('providers_offers', 'providers.provider_id', 'providers_offers.provider_id')
				->whereExpire(0)
				->wherePaid('1')
				->where('providers_offers.publish', 1)
				->select(
					'providers_offers.id AS offer_id',
					'providers_offers.offer_title',
					DB::raw("CONCAT('" . url('/') . "','/offers/',providers_offers.photo) AS offer_photo"),
					'start_date',
					'end_date',
					'providers.provider_id'

				)
				->paginate(10);

		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'offers' => $offers]);
	}

	//// provider jobs functions /////////

	public function get_Jobs(Request $request)
	{
		$lang = $request->input('lang');

		$Jobs = DB::table('providers')
			->join('provider_jobs', 'providers.provider_id', 'provider_jobs.provider_id')
			->where('provider_jobs.publish', 1)
			->select('provider_jobs.id AS job_id',
				'job_title',
				'job_desc',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS store_image")

			)
			->paginate(10);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => '', 'jobs' => $Jobs]);
	}

	public function applyJob(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تم التقديم للوظيفه بنجاح ',
				1 => 'كل الحقول مطلوبه',
				2 => 'امتداد الملف المسموح بها doc,docx,pdf',
				3 => ' اقصي حجم مسموح به للملف هو 2mb',
				4 => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
				5 => 'فشل  التقديم من فضلك حاول لاحقا',
				6 => ' الوظيفه غير موجوده او ربما تم حذفها',
				7 => 'لقد تم التقديم علي هذه الوظيفه من قبل ',
			);

		} else {
			$msg = array(

				0 => 'applied successfully',
				1 => 'All fields are required',
				2 => 'CV file format extension must be only doc,docx,pdf',
				3 => 'CV file max size is 2 M.B',
				4 => 'phone number format invlid it must start with 5 or 05 ',
				5 => 'Failed to apply to job, please try again later',
				6 => 'this job not exists or may be deleted',
				7 => 'This job has already been submitted before',
			);
		}

		$messages = array(
			'required' => 1,
			'cv.mimes' => 2,
			'cv.max' => 3,
			'phone.regex' => 4,
			'job_id.exists' => 6,

		);

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'phone' => array('required', 'regex:/^(05|5)([0-9]{8})$/'),
			'country_code' => 'required',
			'access_token' => 'required',
			'cv' => 'required',
			'job_id' => 'required|exists:provider_jobs,id',

		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$inputs = $request->only('name', 'country_code', 'phone', 'job_id');

		$inputs['user_id'] = $this->get_id($request, 'users', 'user_id');

		if ($request->hasFile('cv')) {

			$file = $request->cv;
			//save new  file
			$file->store('/', 'cvs');

			$nameOfFile = $file->hashName();

			$inputs['cv'] = $nameOfFile;

		}

		if ($this->checkIfAppliedBefore($inputs['user_id'], $request->job_id)) {

			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);

		}

		$id = DB::table('applicants')->insertGetId($inputs);

		if ($id) {
			$applicant = array(
				'name' => $request->input('name'),
				'country_code' => $this->checkCountryCodeFormate($request->input('country_code')),
				'phone' => $request->input('phone'),
				'applicant_id' => $id,
				'cv' => env('APP_URL') . '/public/cvs/' . $nameOfFile,
			);
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'applicant' => $applicant]);
		} else {
			return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		}

	}

	protected function checkIfAppliedBefore($userId, $job_id)
	{

		$exists = DB::table('applicants')->where(['user_id' => $userId, 'job_id' => $job_id])->first();

		if ($exists) {

			return true;
		}

		return false;

	}

	public function search(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لايوجد نتائج لبحثك ',
				2 => 'من فضلك اختر فئة ',
				3 => 'الفئه غير موجوده',
				4 => 'لابد من ادخل السعر من ',
				5 => 'لابد من  ادخال السعر الي ',
				6 => 'السعر من والي لابد ان يكون ارقام ',
				7 => 'السعر الي لابد ان يكون اكبر من الصفر ',
				8 => 'السعر الي لابد ان يكون اكبر من او يساوي السعر من ',
				9 => 'السعر الي لابد ان يكون اكبر من 0 ',

			);
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no result for your search',
				2 => 'Please Choose Category',
				3 => 'Category no exists',
				4 => 'min price required',
				5 => 'max price required',
				6 => 'min price and max price  must be numeric',
				7 => 'max price must be greater than min price',
				8 => 'max price must be greater than or equal to min price',
				9 => 'max price must be greater than 0',

			);
		}

		$messages = array(
			'cat_id.required' => 2,
			'cat_id.exists' => 3,
			'min_price.required' => 4,
			'max_price.required' => 5,
			'min_price.numeric' => 6,
			'max_price.numeric' => 6,
			'max_price.not_in' => 7,
			'max_price.min' => 8,
			'max_price.not_in' => 9,

		);

		$rules = [
			'cat_id' => 'exists:categories_stores,id',
		];

		$conditions = array();

		if ($request->min_price && $request->max_price) {

			//array_push($betweenConditions, ['tble.price', '>', $]);

			$rules['min_price'] = 'required|numeric';
			$rules['max_price'] = 'required|numeric|not_in:0|min:' . $request->min_price;

			array_push($conditions, ['tble.price', '>=', $request->min_price]);
			array_push($conditions, ['tble.price', '<=', $request->max_price]);

		} elseif ($request->min_price && ($request->max_price == 0 || $request->max_price == null || $request->max_price == "")) {

			$rules['min_price'] = 'required|numeric';
			array_push($conditions, ['tble.price', '>=', $request->min_price]);
		} elseif ($request->max_price && ($request->min_price == 0 || $request->min_price == null || $request->min_price == "")) {

			$rules['max_price'] = 'required|numeric|not_in:0';
			array_push($conditions, ['tble.price', '<=', $request->max_price]);
		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$name = $request->input('name');
		$cat = $request->input('cat_id');
		$rate = $request->input('rate');
		$min_price = $request->input('min_price');
		$max_price = $request->input('max_price');

		$virtualTble = "SELECT products.id AS id, products.title AS name, products.category_id AS cat,  providers.longitude AS `long`, providers.latitude AS `lat`, products.product_rate AS rate, products.likes_count AS likes_count,CAST(products.price as decimal(10,2)) AS price FROM `products` JOIN providers ON products.provider_id = providers.provider_id";

		if (!empty($name) && $name !== 0 && $name !== 0.0 && $name !== "0.0") {
			array_push($conditions, ['tble.name', 'like', '%' . $name . '%']);
		}

		if (!empty($cat) && $cat !== 0 && $cat !== "0" && $cat !== 0.0 && $cat !== "0.0") {
			array_push($conditions, ['tble.cat', '=', $cat]);
		}

		if (!empty($rate)) {
			array_push($conditions, ['tble.rate', '=', $rate]);
		}

		if (!empty($conditions)) {
			$result = DB::table(DB::raw("(" . $virtualTble . ") AS tble"))
				->select('tble.id', 'tble.name', 'tble.rate', 'tble.price')
				->where($conditions)->get();

		} else {
			$result = DB::table(DB::raw("(" . $virtualTble . ") AS tble"))
				->select('tble.id', 'tble.name', 'tble.rate', 'tble.price')
				->get();
		}

		if (isset($result) && $result->count() > 0) {

			foreach ($result as $key => $product) {

				$mainImge = DB::table('product_images')->where('product_id', $product->id)->select('image')->first();
				if ($mainImge) {

					$product->img = env('APP_URL') . '/public/products/' . $mainImge->image;

				} else {

					$product->img = '';

				}

			}
		}

		// if(!empty($result)){
		//     foreach($result AS $row){
		//         $row->price = round($row->price, 2);
		//         // $row->price = $row->price + 0.00;
		//     }
		// }

		if (isset($result) && $result->count() > 0) {
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'result' => $result]);
		} else {
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}

	public function prepareSearch(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
			);
			$cat_col = "store_cat_ar_name AS cat_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
			);
			$cat_col = "store_cat_en_name AS cat_name";
		}

		$categories = DB::table('categories_stores')
			->where('categories_stores.publish', 1)
			->join('products', 'categories_stores.id', '=', 'products.category_id')
			->select('categories_stores.id',
				$cat_col,
				DB::raw("(SELECT count(products.id) FROM products WHERE products.category_id = categories_stores.id) AS product_count")
			)->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'cats' => $categories]);
	}

	//method to add user order
	public function addOrder(Request $request)
	{

		//  total price is total price of order products
		//  total value is  total price + delivery_price
		// net value is product value - app value   net which provider earn

		//get app percentage
		$app_settings = DB::table('app_settings')->first();

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'تمت العملية بنجاح',
				1 => 'لا يوجد  منتجات في الطلب',
				2 => 'خطأ فى  منتج ما  عدم وجود رقم للمنتج ',
				3 => 'خطأ فى سعر  منتج  ما',
				4 => 'خطأ فى العدد من  منتج  ما',
				5 => 'كل البيانات مطلوبه',
				6 => 'خانة lang يجب ان تكون واحده من (ar, en)',
				7 => 'خانة in_future يجب ان تكون واحده من (0, 1)',
				8 => 'خانة delivery_time يجب ان تكون بنستيق (Y-m-d H:i:s)',
				9 => 'حدث خطأ ما من فضلك حاول فى وقت لاحق',
				10 => 'رقم المنتج  لا يمكن ان يتكرر',
				11 => 'لا يوجد كمية كافية للعدد المطلوب فى هذه الوجبات',
				12 => 'مقدم الخدمه لا يقوم بإستلام طلبات',
				13 => 'الوجبات يجب ان تكون على شكل مصفوفه',
				14 => 'خطأ فى العنوان',
				15 => 'balance_flag يجب ان يكون 0 او 1',
				16 => '  معفوا وقت الطلب خارج مواعيد عمل التاجر  ',
				17 => 'موعدد الطلب لابد ان يكون اكبر من الان ',
				18 => 'المستخدم غير موجود ',
				19 => 'خطا ببيانات المستخدم ',
				20 => 'حجم  المنتج  غير موجود ',
				21 => 'لون المنتج غير موجود ',
				22 => 'الاضافات غير موجوده ',
				23 => 'القيمه المدفوعه لا تساوي قيمه الطلب ',
				24 => 'كمية المنتج اكبر من الكمية فى المخزن',
			);
			// $push_notif_title = "طلب جديد";
			// $push_notif_message = "تم إضافة طلب جديد ";
		} else {
			$msg = array(
				0 => 'Process done successfully',
				1 => 'There is no any products in order',
				2 => 'There is an error in some prodcut id',
				3 => 'There is an error in some product price',
				4 => 'There is an error in some product count number',
				5 => 'All data is required',
				6 => 'lang field must be one of (ar, en)',
				8 => 'delivery_time field must be in format (Y-m-d H:i:s)',
				9 => 'There is something wrong, please try again later',
				10 => 'product_id can not be repeated',
				11 => 'There is no enough quantaty for these meals',
				12 => 'The provider doesn\'t receive orders',
				13 => 'Meals must be an array',
				14 => 'invalid address',
				15 => 'balance_flag must be 0 or 1',
				16 => 'The time of the request outside the merchant\'s working hour',
				17 => 'delivery time must be greater or equal to now',
				18 => 'login user not found',
				19 => 'User Data Error',
				20 => 'product_size not exists',
				21 => 'product_color not exists',
				22 => 'product_options not exist',
				23 => 'Paid amount not equal to order amount ',
				24 => 'Product quantity is larger than quantity in stock',

			);
			// $push_notif_title = "New order";
			// $push_notif_message = "A new order has been added  ";
		}

		$products = $request->input('products');
		$provider = $request->input('provider_id');
		$user = $this->get_id($request, 'users', 'user_id');

		if ($user == 0) {

			return response()->json(['status' => false, 'errNum' => 18, 'msg' => $msg[18]]);
		}

		$address = $request->input('address'); // user address id
		$delivery_method = $request->input('delivery_method_id');
		$payment_method = $request->input('payment_method_id');
		$delivery_time = $request->input('delivery_time');
		$totalQty = 0;
//		$totalPrice = 0;
		$net = 0;
		$totalValue = 0;
		$totalDisc = 0;

		if (empty($products)) {
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}

		if (!is_array($products)) {
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}

		$productsArr = array();
		$invalid_products = array();

		//first step need to validate all array indexs

		for ($i = 0; $i < count($products); $i++) {
			array_push($productsArr, $products[$i]['product_id']);

			if (empty($products[$i]['product_id'])) {
				return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
			}

			if (empty($products[$i]['price'])) {
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}

			if (empty($products[$i]['qty'])) {
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}

			$quantity_in_stock = DB::table("products")
				->where("id", $products[$i]['product_id'])
				->select("quantity")
				->first();

			if (!empty($products[$i]['qty']) && intval ($products[$i]['qty']) > intval ($quantity_in_stock->quantity)) {
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[24]]);
			}

			// if product  with size  the price will change
			$productPrice = $products[$i]['price'];
			$productSize = 0;
			$productColor = 0;

			if (empty($products[$i]['size'])) {

			} else {

				$size_id = DB::table("product_sizes")
					->where("id", $products[$i]['size'])
					->where("product_id", $products[$i]['product_id'])
					->select("id", "price")
					->first();

				if (!$size_id) {
					return response()->json([
						"status" => false,
						"errNum" => 20,
						"msg" => $msg[20],
					]);
				}
				$productPrice += $size_id->price;
				$productSize = $size_id->id;
			}

			// if product  with color the price will change
			if (empty($products[$i]['color'])) {

			} else {

				$color_id = DB::table("product_colors")
					->where("id", $products[$i]['color'])
					->where("product_id", $products[$i]['product_id'])
					->select("id", "price")
					->first();

				if (!$color_id) {
					return response()->json([
						"status" => false,
						"errNum" => 21,
						"msg" => $msg[21],
					]);
				}
				$productPrice += $color_id->price;
				$productColor = $color_id->id;
			}

			// if there are any other adds then some add prices to default product price
			$options_arr = [];
			$options_added_price = 0;

			//  products[0][options][0][id]

			if (!empty($products[$i]['options']) && is_array($products[$i]['options'])) {

				//return $products[$i]['options'];

				foreach ($products[$i]['options'] as $option) {

					if (empty($option)) {
						return response()->json([
							"status" => false,
							"errNum" => 5,
							"msg" => $msg[5], // improve error message  in future
						]);
					}

					$option_id = DB::table("product_options")
						->where("id", $option)
						->where("product_id", $products[$i]['product_id'])
						->select("id", "price")
						->first();

					if (!$option_id) {
						return response()->json([
							"status" => false,
							"errNum" => 22,
							"msg" => $msg[22],
						]);
					}
					$options_arr[] = [
						"id" => $option_id->id,
						"added_price" => $option_id->price,
						"product_id" => $products[$i]['product_id'],
					];
					$options_added_price += $option_id->price;
				}
			}

			if (empty($products[$i]['discount']) || $products[$i]['discount'] == "0" || $products[$i]['discount'] == "") {
				$products[$i]['discount'] = 0;
			}

			//second step calculate total qty and price and disc
			$totalQty += $products[$i]['qty'];
//			$totalPrice += ($productPrice + $options_added_price) * $products[$i]['qty'];
//			$totalPrice += ((int)$app_settings->tax * $totalPrice) / 100;

			$totalDisc += $products[$i]['discount'];
			$net += $products[$i]['qty'] * ($productPrice + $options_added_price); // need to subtract the discount from the net value

		} //end foreach

		$uniqueProducts = array_values(array_unique($productsArr)); // to avoid duplicate products id

		if (count($productsArr) != count($uniqueProducts)) {
			return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		}

		$messages = array(
			'required' => 5,
			'lang.in' => 6,
			'in_future.in' => 7,
			'date_format' => 8,
			'exists' => 14,
			'balance_flag.in' => 15,
			'after_or_equal' => 17,
		);

		$rules = [
			'provider_id' => 'required',
			'access_token' => 'required',
			//'in_future'          => 'required|in:0,1',
			'address' => 'required|exists:user_addresses,address_id',
			'delivery_method_id' => 'required',
			'payment_method_id' => 'required',
			//'balance_flag'          => 'required|in:0,1',
			//'delivery_time'      => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d H:i:s'),
		];

		if ($request->input("payment_method") == 2 || $request->input("payment_method") == 3) {
			$rules['total_paid_amount'] = "required";
//			$rules['process_number'] = "required";
		}

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		//get user address data
		$userAdress = DB::table('user_addresses')->where('address_id', $address)->first();
		//chech if the provider accept orders or not
		$conditions[] = ['provider_id', '=', $request->input("provider_id")];
		//$conditions[] = ['receive_orders', '=', 1];

		//$conditions[] = ['current_orders', '=', 1];
		$check = Providers::where($conditions)
			->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 12, 'msg' => $msg[12]]);
		}
		$provider_longitude = $check->longitude;
		$provider_latitude = $check->latitude;
		$provider_reg_id = $check->device_reg_id;
		$provider_delivery_price = $check->delivery_price;
		$marketer_code = $check->marketer_code;
		$created = date('Y-m-d', strtotime($check->created_at));

		$visitor_address_lat = $userAdress->latitude;
		$visitor_address_long = $userAdress->longitude;

		$delivery_price = 0;
		$orderCode = mt_rand();

		/*delivery has three cases
        1- recieve from store so delivery_price =0
        2- recieve via store delivery  then delivery_price = this provider delivery price
        3- app delivery    then   delivery_price = admin delivery_price value

         */

		if ($app_settings) {
			$percentage = $app_settings->app_percentage;
			$kilo_price = $app_settings->kilo_price;
			$delivery_percentage = $app_settings->delivery_percentage;
			$initial_price = 0;
		} else {
			$percentage = 0;
			$kilo_price = 0;
			$delivery_percentage = 0;
			$initial_price = 0;
		}

		// 1 is recieve order from store no delivery fees
		if ($delivery_method == 1) {
			$delivery_price = 0;

			//2 store deliver order to users
		} elseif ($delivery_method == 2) {
			$delivery_price = $provider_delivery_price;
		} elseif ($delivery_method == 3) {

			$dKilos = $this->distance($visitor_address_lat, $visitor_address_long, $provider_latitude, $provider_longitude, false);
			$delivery_price = ROUND(($dKilos * $kilo_price), 2);
		}

		$app_value = ($net * $percentage) / 100;

		// app get 2% of delivery
		$delivery_app_value = ($delivery_price == 0) ? 0 : (($delivery_price * $delivery_percentage) / 100);

		$total_value = $net + $delivery_price;
		$net = $net - $app_value;

		$data['provider_marketer_code'] = "";
		$provider_marketer_value = 0;
		$points = 0;

		$data['process_number'] = '';

		// if payment by visa must ensure paid amount equal order total value
		if ($payment_method == 2 || $payment_method == 3) {

			/*			if ($request->input("total_paid_amount") != $totalPrice) {
							return response()->json([
								"status" => false,
								"errNum" => 23,
								"msg" => $msg[23],
							]);
						}*/

			// $data['process_number'] = $request->input("process_number");
			$data['process_number'] = mt_rand();
		}

		//we will set this to zero till split payment method is activated
		$split_value = 0;
		try {
			$data['totalPrice'] = $request->input("total_paid_amount");
			$data['totalQty'] = $totalQty;
			$data['totalDisc'] = $totalDisc;
			$data['net'] = $net;
			$data['delivery_price'] = $delivery_price;
			$data['total_value'] = $total_value;
			$data['app_value'] = $app_value;
			$data['percentage'] = $percentage;
			$data['delivery_percentage'] = $delivery_percentage;
			$data['delivery_app_value'] = $delivery_app_value;
			$data['user'] = $user;
			$data['points'] = $points;
			$data['provider'] = $provider;
			$data['address'] = $userAdress->address;
			$data['user_longitude'] = $userAdress->longitude;
			$data['user_latitude'] = $userAdress->latitude;
			$data['payment_method'] = $payment_method;
			$data['delivery_method'] = $delivery_method;
			$data['orderCode'] = $orderCode;
			$data['in_future'] = 0;
			$data['split_value'] = $split_value;
			$data['products'] = $products;
			$data['balance_flag'] = 0; // this app not use poins and balances

			$userInfo = DB::table('user_addresses')
				->join('users', 'user_addresses.user_id', '=', 'users.user_id')
				->where('user_addresses.user_id', $user)
				->select('user_addresses.phone', 'user_addresses.address', 'users.email')
				->first();

			if (!$userInfo) {
				return response()->json(['status' => false, 'errNum' => 19, 'msg' => $msg[19]]);
			}

			$data['phone'] = $userInfo->phone;
			$data['email'] = $userInfo->email;
			$data['marketer_percentage'] = 0;
			$data['provider_marketer_value'] = 0;
			$id = 0;

			DB::transaction(function () use ($data, $options_arr, &$id) {
				//setting order header

				$used_points = 0;

				$id = DB::table('orders_headers')->insertGetId([
					'total_price' => $data['totalPrice'],
					'total_qty' => $data['totalQty'],
					'total_value' => $data['total_value'],
					'used_points' => $used_points,
					'net_value' => $data['net'],
					'app_percentage' => $data['percentage'],
					'app_value' => $data['app_value'],
					'delivery_price' => $data['delivery_price'],
					'total_discount' => $data['totalDisc'],
					'user_id' => $data['user'],
					'provider_id' => $data['provider'],
					'address' => $data['address'],
					'order_code' => $data['orderCode'],
					'user_latitude' => $data['user_latitude'],
					'user_longitude' => $data['user_longitude'],
					'user_phone' => $data['phone'],
					//'user_email'             => $data['email'],
					'payment_type' => $data['payment_method'],
					'delivery_method' => $data['delivery_method'],
					'in_future' => $data['in_future'],
					'split_value' => $data['split_value'],
					'delivery_app_value' => $data['delivery_app_value'],
					'delivery_app_percentage' => $data['delivery_percentage'],
					'marketer_percentage' => $data['marketer_percentage'],
					'marketer_value' => $data['provider_marketer_value'],
					'provider_marketer_code' => $data['provider_marketer_code'],
					'process_number' => $data['process_number'] == '' ? null : $data['process_number'],
				]);
				$serial = 1;

				$productsArr = $data['products'];

				for ($i = 0; $i < count($productsArr); $i++) {

					DB::table('order_products')->insert([
						'order_id' => $id,
						'product_id' => $productsArr[$i]['product_id'],
						'product_price' => $productsArr[$i]['price'],
						'qty' => $productsArr[$i]['qty'],
						'discount' => $productsArr[$i]['discount'],
						'size_id' => $productsArr[$i]['size'],
						'color_id' => $productsArr[$i]['color'],
						'serial' => $serial,
					]);

					###### Start Update Product Quantity In Stock #######

					$quantity_in_stock = DB::table("products")
						->where("id", $productsArr[$i]['product_id'])
						->select("quantity")
						->first();

					DB::table('products')
						->where('product_id', $productsArr[$i]['product_id'])
						->update([
							'quantity' => intval ($quantity_in_stock->quantity) - intval ($productsArr[$i]['qty']),
						]);

					###### End Update Product Quantity In Stock #######

					$serial++;
				}

				if (!empty($options_arr)) {

					foreach ($options_arr as $insertOptions) {
						DB::table("order_products_options")
							->insert([
								"order_id" => $id,
								"option_id" => $insertOptions['id'],
								"option_price" => $insertOptions['added_price'],
								"product_id" => $insertOptions['product_id'],
							]);
					}
				}

			});

			$provider_token = Providers::where('provider_id', $data['provider'])->first();

			$recieverAppLang = $provider_token->lang;

			if ($recieverAppLang == 'ar') {
				$push_notif_title = "طلب جديد";
				$push_notif_message = "تم إضافة طلب جديد ";
			} else {
				$push_notif_title = "New order";
				$push_notif_message = "A new order has been added  ";
			}

			$notif_data = array();
			$notif_data['title'] = $push_notif_title . '-' . $id;
			$notif_data['message'] = $push_notif_message;
			$notif_data['order_id'] = $id;
			$notif_data['notif_type'] = 'order';

			$providerAllowNewOrderNotify = (new NotifyC())->check_notification($data['provider'], 'providers', 'new_order');

			if ($provider_token && $providerAllowNewOrderNotify == 1) {
				$push_notif = (new Push())->send($provider_token->device_reg_id, $notif_data, (new Push())->provider_key);
			}

			if ($providerAllowNewOrderNotify == 1) {

				DB::table("notifications")
					->insert([
						"en_title" => $push_notif_title . '-' . $id,
						"ar_title" => $push_notif_title . '-' . $id,
						"en_content" => $push_notif_message,
						"ar_content" => $push_notif_message,
						"notification_type" => 1,
						"actor_id" => $data['provider'],
						"actor_type" => "provider",
						"action_id" => $id,

					]);
			}

			//send notification to all deliveries  and push notification

			$this->sendNotificationToDeliveries($notif_data, $id, $push_notif_title, $push_notif_message);

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'order_id' => $id]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
		}

	}

	protected function sendNotificationToDeliveries($notif_data, $id, $push_notif_title, $push_notif_message)
	{

		$deliveries = DB::table('deliveries')->join('notification_settings', 'deliveries.delivery_id', '=', 'notification_settings.actor_id')->where('notification_settings.type', 'deliveries')->where('notification_settings.new_order', 1)->get();

		if (isset($deliveries) && $deliveries->count() > 0) {

			foreach ($deliveries as $key => $delivery) {

				if ($delivery->device_reg_id) {

					$push_notif = (new Push())->send($delivery->device_reg_id, $notif_data, (new Push())->delivery_key);

				}

				DB::table("notifications")
					->insert([
						"en_title" => $push_notif_title . '-' . $id,
						"ar_title" => $push_notif_title . '-' . $id,
						"en_content" => $push_notif_message,
						"ar_content" => $push_notif_message,
						"notification_type" => 1,
						"actor_id" => $delivery->delivery_id,
						"actor_type" => "delivery",
						"action_id" => $id,

					]);

			}
		}

	}

	public function get_list_of_orders(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => '',
				1 => 'رقم مقدم الخدمه مطلوب',
				2 => 'نوع الطلبات مطلوب',
				3 => 'نوع العمليه يجب ان يكون  0,1',
				4 => 'لا يوجد طلبات بعد',
				5 => ' المستخدم  غير موجود ',
			);
			$payment_col = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col = "order_status.ar_desc AS status_text";
		} else {
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'type is required',
				3 => 'type must be 0,1 ',
				4 => 'There is no ordes yet',
				5 => 'user not Found',
			);
			$payment_col = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col = "order_status.en_desc AS status_text";
		}

		$messages = array(
			'access_token.required' => 1,
			'type.required' => 2,
			'in' => 3,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
			'type' => 'required|in:0,1',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user_id = $this->get_id($request, 'users', 'user_id');

		if ($user_id == 0) {
			return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		}

		$check = DB::table('users')->where('user_id', $user_id)->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		}

		$type = $request->input("type");
		// 0 -> current   "1" -> pendings   and  "2"  -> approved
		// 1 -> Previous  "3" -> delivered  and "4"  ->  cancelled

		$inCondition = [];
		if ($type == 0) {

			$inCondition = [1, 2];

			//  array_push($conditions, [DB::raw('orders_headers.created_at') , '>', Carbon::now()->addHours(1)->subMinutes($time_counter_in_min)]);

		} elseif ($type == 1) {
			$inCondition = [3, 4];
			//array_push($conditions, [DB::raw('DATE(orders_headers.expected_delivery_time)') , '<=', $today]);
		}

		$conditions[] = ['users.user_id', '=', $user_id];

		//get orders
		$orders = \App\Order_header::where($conditions)
			->whereIn('orders_headers.status_id', $inCondition)
			->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
			->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
			->join('users', 'orders_headers.user_id', 'users.user_id')
			->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
			->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
			->leftJoin('rejectedorders_delivery', 'orders_headers.order_id', '=', 'rejectedorders_delivery.order_id')
			->select(
				'orders_headers.order_id',
				'orders_headers.order_code',
				'orders_headers.delivery_id',
				'providers.store_name AS store_name',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/', providers.profile_pic) AS store_image"),
				'orders_headers.address',
				'users.full_name AS user_name',
				'orders_headers.total_value',
				$payment_col,
				$delivery_col,
				DB::raw("(SELECT count(order_products.id) FROM order_products WHERE order_products.order_id = orders_headers.order_id) AS products_count"),
				$status_col,
				'orders_headers.status_id',
				DB::raw('DATE(orders_headers.created_at) AS created_date'),
				DB::raw('TIME(orders_headers.created_at) AS created_time'),
				'rejectedorders_delivery.status AS delivery_status'
			)
			->orderBy('orders_headers.order_id', 'DESC')
			->paginate(10);

		return response()->json([
			'status' => true,
			'errNum' => 0,
			'msg' => $msg[0],
			'orders' => $orders,

		]);
	}

	public function cancel_order(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(

				0 => 'تمت إلغاء الطلب',
				1 => 'order_id مطلوب',
				2 => 'user_id مطلوب',
				3 => 'فشل إلغاء الطلب حاول مره اخرى',
				4 => 'لا يمكنك إلغاء إلا طلباتك',
				5 => 'عفوا لا يمكن إلغاء  بعد موافقه التاجر عليها ',
				6 => 'عفوا لا يمكن إلغاء طلب منتهى',
				7 => 'المستخدم غير موجود ',
				8 => 'النوع مطلوب ',
				9 => 'النوع لابد ان يكون  providers, users',
				10 => 'الطلب غير موجود ',
				11 => 'لقج تم الغاء الطلب مسبقا ',
				12 => 'لابد من ادخال سبب الرفض ',
			);
			// $push_notif_title = 'إلغاء طلب';
			// $push_notif_message = 'تم إلغاء  الطلب رقم  بسبب ';
		} else {
			$msg = array(
				0 => 'Order has been canceled',
				1 => 'order_id is required',
				2 => 'user_id is required',
				3 => 'Failed to cancel the order, try again',
				4 => 'Sorry it is not your order to cancel',
				5 => 'Sorry you can\'t cancel Order Approved By provider',
				6 => 'Sorry you can\'t cancel finished order',
				7 => 'User Not Found',
				8 => 'Type Field required',
				9 => 'Type must be in providers , users',
				10 => 'Order not found',
				11 => 'Order Already cancelled',
				12 => 'Must Enter Cancellation reason',
			);

			// $push_notif_title = 'Order canceled';
			// $push_notif_message = 'the order has been cancelled  because ';
		}

		$messages = array(
			'order_id.required' => 1,
			'access_token.required' => 2,
			'type.required' => 8,
			'type.in' => 9,
			'reason.required' => 12,
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required',
			'access_token' => 'required',
			'type' => 'required|in:users,providers,deliveries',
			'reason' => 'required',

		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$type = $request->input('type');

		switch ($type) {
			case 'providers':
				$actor = 'providers';
				$table = 'providers';
				$colum = 'provider_id';
				$key = 'user_key';
				$notify_actor_type = 'user';
				$notify_type_table = 'users';
				$notify_column = 'user_id';

				break;

			case 'users':
				$actor = 'users';
				$table = 'users';
				$colum = 'user_id';
				$key = 'provider_key';
				$notify_actor_type = 'provider';
				$notify_type_table = 'providers';
				$notify_column = 'provider_id';

				break;

			default:
				$actor = 'users';
				$table = 'users';
				$colum = 'user_id';
				$key = 'provider_key';

				$notify_actor_type = 'provider';
				$notify_type_table = 'providers';
				$notify_column = 'provider_id';

				break;
		}

		$actor_id = $this->get_id($request, $table, $colum);

		if ($actor_id == 0) {
			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		}

		$check = DB::table($table)->where($colum, $actor_id)->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 7, 'msg' => $msg[7]]);
		}

		//make sure that the order is the user/provider whoes cancel  order
		$check = DB::table('orders_headers')->where($colum, $actor_id)
			->where('order_id', $request->input('order_id'))
			->select('status_id', 'user_id', 'provider_id', 'payment_type', 'total_value')
			->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
		}

		if ($check->status_id == 3 || $check->status_id == 4) {

			return response()->json(['status' => false, 'errNum' => 6, 'msg' => $msg[6]]);

		} elseif ($check->status_id == 2) {

			return response()->json(['status' => false, 'errNum' => 5, 'msg' => $msg[5]]);
		}

		$actor_id = $check->$notify_column;
		$payment_type = $check->payment_type;
		$total_value = $check->total_value;

		try {

			$order_id = $request->input('order_id');
			$rejected_reason = $request->input('reason');
			$status = 4; // cancel status

			DB::transaction(function () use ($status, $order_id, $rejected_reason, $actor_id, $payment_type, $total_value) {

				DB::table("orders_headers")->where('order_id', $order_id)->update([
					'status_id' => $status,
					'rejected_reason' => !empty($rejected_reason) ? $rejected_reason : null,
				]);

				DB::table("order_products")->where('order_id', $order_id)->update(['status' => $status]);

			});


			$actor_token = DB::table($notify_type_table)->where($notify_column, $actor_id)->first();
			$recieverAppLang = $actor_token->lang;

			if ($recieverAppLang == 'ar') {
				$push_notif_title = 'إلغاء طلب';
				$push_notif_message = 'تم إلغاء  الطلب رقم  بسبب ';
			} else {
				$push_notif_title = 'Order canceled';
				$push_notif_message = 'the order has been cancelled because ';
			}

			$notif_data = array();
			$notif_data['title'] = $push_notif_title . '-' . $order_id;
			$notif_data['message'] = $push_notif_message . '-' . $request->reason;
			$notif_data['order_id'] = $order_id;
			$notif_data['notif_type'] = 'cancel_order';

			/*
            if($provider_token && $providerAllowNewOrderNotify == 1){
            $push_notif =(new Push())->send($provider_token->device_reg_id, $notif_data,(new Push())->provider_key);
            }
             */

			$actorAllowcancelOrderNotify = 1;

			if ($notify_type_table == 'providers') {

				$actorAllowcancelOrderNotify = (new NotifyC())->check_notification($actor_id, 'providers', 'cancelled_order');

			} elseif ($notify_type_table == 'users') {
				$actorAllowcancelOrderNotify = (new NotifyC())->check_notification($actor_id, 'users', 'order_status_user');
			}

			if (!$actor_token && $actorAllowcancelOrderNotify == 1) {

				$push_notif = (new Push())->send($actor_token->device_reg_id, $notif_data, (new Push())->$key);

			}

			if ($actorAllowcancelOrderNotify == 1) {

				DB::table("notifications")
					->insert([
						"en_title" => $push_notif_title . '-' . $order_id,
						"ar_title" => $push_notif_title . '-' . $order_id,
						"en_content" => $push_notif_message . '-' . $request->reason,
						"ar_content" => $push_notif_message . '-' . $request->reason,
						"notification_type" => 1,
						"actor_id" => $actor_token->$notify_column,
						"actor_type" => $notify_actor_type,
						"action_id" => $order_id,

					]);

			}

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
		} catch (Exception $e) {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}
	}

	public function getOrderDetails(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد تفاصيل',
				2 => 'رقم الاوردر مطلوب',
				3 => 'الطلب غير موجود',
			);
			$payment_col = "payment_types.payment_ar_name AS payment_method";
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$status_col = 'order_status.ar_desc AS order_status';
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no order details!!',
				2 => 'order_id is required',
				3 => 'order not exists',
			);
			$payment_col = "payment_types.payment_en_name AS payment_method";
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$status_col = 'order_status.en_desc AS order_status';
		}

		$messages = array(
			'required' => 2,
			'exists' => 3,
		);

		$validator = Validator::make($request->all(), [
			'order_id' => 'required|exists:orders_headers,order_id',
		], $messages);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$error = $errors->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}
		//get order header
		$order = DB::table('orders_headers')
			->where('orders_headers.order_id', $request->input('order_id'))
			->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
			->join('payment_types', 'orders_headers.payment_type', '=', 'payment_types.payment_id')
			->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
			->join('users', 'orders_headers.user_id', '=', 'users.user_id')
			->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
			->leftJoin('rejectedorders_delivery', 'orders_headers.order_id', '=', 'rejectedorders_delivery.order_id')
			->select('orders_headers.order_code',
				'orders_headers.order_id',
				'orders_headers.provider_id',
				'orders_headers.status_id',
				'orders_headers.rejected_reason',
				$status_col,
				'orders_headers.total_value AS total',
				'orders_headers.net_value AS net_value', // order price with out any delivery price just products with options
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
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS store_image"),
				'providers.longitude AS provider_longitude',
				'providers.latitude AS provider_latitude',
				'providers.membership_id',
				DB::raw('IFNULL(DATE(orders_headers.created_at), "") AS order_date'),
				DB::raw('IFNULL(TIME(orders_headers.created_at), "") AS order_time'),
				'rejectedorders_delivery.status AS delivery_status'

			)
			->first();

		//    dd($header);

		$products = DB::table('order_products')->where('order_products.order_id', $request->input('order_id'))
			->join('products', 'order_products.product_id', '=', 'products.id')
			->select(
				'order_products.qty',
				'products.title',
				'products.description',
				'order_products.product_price',
				'order_products.discount',
				'products.id as product_id',
				'order_products.size_id',
				'order_products.color_id'
			)
			->get();

		if (isset($products) && $products->count() > 0) {

			foreach ($products as $product) {

				//with size

				$size = [];
				$color = [];
				$size = DB::table('product_sizes')->whereProduct_id($product->product_id)->whereId($product->size_id)->select('product_sizes.name', 'product_sizes.id', 'product_sizes.price')->first();

				//with color
				$color = DB::table('product_colors')->whereProduct_id($product->product_id)->whereId($product->color_id)->select('product_colors.name', 'product_colors.id', 'product_colors.price')->first();

				//with options

				$options = DB::table('order_products_options')->where('order_id', $request->order_id)->join('product_options', 'product_options.id', '=', 'order_products_options.option_id')->select('option_id as id', 'name', 'price')->where('order_products_options.product_id', $product->product_id)->get();

				$product->size = $size;
				$product->color = $color;
				$product->options = $options;

				$image = DB::table('product_images')->where('product_id', $product->product_id)->first();

				if ($image) {

					$product->main_image = env('APP_URL') . '/public/products/' . $image->image;

				} else {

					$product->main_image = "";

				}

			}

		}

		//return response()->json(["dataa" , $details]);

		if ($order) {
			$status = $order->status_id;
		} else {
			$status = "";
		}

		//get rate only if order status is deliveried

		if ($status == 3 || $status == "3") {

			$provider_order_rate = DB::table('providers_rates')
				->where('order_id', $request->input('order_id'))
				->select(
					DB::raw("IFNULL((rates), 0) AS order_rate"),
					DB::raw("IFNULL(((comment)), 0) AS comment"))
				->first();

			if ($provider_order_rate) {
				$provider_order_rate = [
					"rate" => $provider_order_rate->order_rate,
					"comment" => $provider_order_rate->comment,
				];
			} else {
				$provider_order_rate = [];
			}

		} else {
			$provider_order_rate = [];
		}

		// $order_status = DB::table('order_status')->whereIn('status_id', [1, 2, 3, 4])
		$order_status = DB::table('order_status')->whereIn('status_id', [2, 3, 4])
			->select(
				'status_id',
				$status_col,
				DB::raw('IF(status_id = ' . $order->status_id . ', true, false) AS choosen')
			)->get();

		$percentage = DB::table('app_settings')->select('app_percentage', 'tax')->first();

		if ($percentage) {
			$app_percentage = $percentage->app_percentage;
			if ($order) {

				$order->tax = $percentage->tax;
			}
		} else {
			$app_percentage = 0;
		}

		$provider_rate_sum = DB::table('providers_rates')->where('provider_id', $order->provider_id)->sum('rates');
		$provider_rate_count = DB::table('providers_rates')->where('provider_id', $order->provider_id)->count('rates');

		$provider_rate = $provider_rate_count == 0 ? 0 : round($provider_rate_sum / $provider_rate_count);

		//$orderOptions = DB::table('order_products_options') -> where('order_id',$request->input('order_id')) -> join('product_options','order_products_options.option_id','=','product_options.id') -> select('product_options.name','product_options.id','product_options.price')  -> get();

		return response()->json([
			'status' => true,
			'errNum' => 0,
			'msg' => 'Retrieved successfully',
			'order' => $order,
			'products' => $products,
			'app_percentage' => $app_percentage,
			'order_status' => $order_status,
			'provider_order_rate' => $provider_order_rate,
			'provider_rate' => $provider_rate,

		]);

	}

	public function CheckWorkingHoursForProvider($providerId, $delivery_time, $type)
	{

		$result = array();

		$provider = Providers::where('provider_id', $providerId)->first();

		$provider_future_date = $provider->avail_date;

		$delivery_date_time = DateTime::createFromFormat("Y-m-d H:i:s", $delivery_time);

		$delivery_timenew = $delivery_date_time->format("H-i-s");
		$delivery_date = $delivery_date_time->format("Y-m-d");

		$start = $provider->allowed_from_time;
		$end = $provider->allowed_to_time;

		if ($start == null) {
			$start = '09:00';
		}

		if ($end == null) {
			$end = '23:30';
		}

		$result['providerfromTime'] = $start;
		$result['providertoTime'] = $end;
		$result['providerFutureDate'] = $provider_future_date;
		$result['type'] = 'current_order';
		$result['status'] = true;

		if ($type == 'future_order') {

			if ($delivery_date <= $provider_future_date) {

				$result['status'] = true;
				$result['type'] = 'future_order';

				return $result;

			} else {

				$result['status'] = false;
				$result['type'] = 'future_order';

				return $result;
			}

		}

		if ($start <= $delivery_timenew && $delivery_timenew <= $end) {

			$result['status'] = true;

			return $result;
		}

		$result['status'] = false;

		return $result;

	}

	public function getCountries(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
			);
			$col = "country_ar_name AS country_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
			);
			$col = "country_en_name AS country_name";
		}

		$countries = DB::table('country')->select('country_id', $col, 'country_code')->get();

		if ($countries->count()) {
			return response()->json(['status' => true, 'errNum' => 0, $msg[0], 'countries' => $countries]);
		} else {
			return response()->json(['status' => false, 'errNum' => 1, $msg[1]]);
		}
	}

	public function countryCityies(Request $request)
	{
		$lang = $request->input('lang');
		$country = $request->input('country_id');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
				2 => 'رقم الدوله مطلوب',
			);
			$col = "city.city_ar_name AS city_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
				2 => 'country_id is required',
			);
			$col = "city.city_en_name AS city_name";
		}

		if (empty($country) || $country == null) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		} else {
			$cities = DB::table('city')->where('city.country_id', $country)
				->join('country', 'city.country_id', '=', 'country.country_id')
				->select('city.city_id', $col, 'country.country_code')->get();

			if ($cities->count()) {
				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'cities' => $cities]);
			} else {
				return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
			}
		}

	}

	public function cities(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
			);
			$city_col = "city.city_ar_name AS city_name";
			$country_col = "country_ar_name AS country_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
			);
			$city_col = "city.city_en_name AS city_name";
			$country_col = "country_en_name AS country_name";
		}
		$resultArr = array();
		$tmpArr = array();
		//get all countries
		$countries = DB::table('country')->select('country_id', $country_col)->get();
		if ($countries->count()) {
			foreach ($countries as $country) {
				$cities = DB::table('city')->where('city.country_id', $country->country_id)
					->join('country', 'city.country_id', '=', 'country.country_id')
					->select('city.city_id', 'city.city_abbreviation', $city_col, 'country.country_code')->get();
				if ($cities->count()) {
					$resultArr[$country->country_name] = $cities;
				} else {
					$resultArr[$country->country_name] = [];
				}
			}
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'cities' => $resultArr]);
		} else {
			return response()->json(['status' => false, 'errNum' => 1, 'msg' => $msg[1]]);
		}
	}

	public function preparePayment(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
				2 => 'رقم المستخدم مطلوب',
				3 => 'رقم المقدم مطلوب',
				5 => 'رقم المستخدم غير موجود',
				3 => 'رقم المقدم غير موجود',
			);
			$city_col = "city_ar_name AS city_name";
			$delivery_col = "method_ar_name AS delivery_name";
			$cat_col = "cat_ar_name AS cat_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
				2 => 'user_id is required',
				3 => 'provider_id is required',
				4 => 'user_id is not valid',
				5 => 'provider_id is not valid',
			);
			$city_col = "city_en_name AS city_name";
			$delivery_col = "method_en_name AS delivery_name";
		}

		$messages = array(
			'user_id.required' => 2,
			'provider_id.required' => 3,
			'user_id.exists' => 4,
			'provider_id.exists' => 5,
		);
		$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			"provider_id" => 'required|exists:providers',
		], $messages);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}
		// get provider enable future order or not
		$provider_info = DB::table("providers")
			->where("provider_id", $request->input("provider_id"))
			->select("future_orders")
			->first();
		$deliveries = DB::table("delivery_methods")->join('providers_delivery_methods', 'providers_delivery_methods.delivery_method', '=', 'delivery_methods.method_id')->select('method_id', $delivery_col)->
		where('provider_id', $request->input('provider_id'))->get();

		if ($request->input("user_id") == "0") {
			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => [], 'deliveries' => $deliveries, "is_provider_allow_future_orders" => $provider_info->future_orders]);
		} else {
			$user = DB::table("users")
				->where("user_id", $request->input("user_id"))
				->first();
			if (!$user) {
				return response()->json(['status' => false, 'errNum' => 4, 'msg' => $msg[4]]);
			}
		}
		$addresses = DB::table('user_addresses')->where('user_id', $request->input('user_id'))
			->select('address_id', 'user_id', 'short_desc AS short_address', 'address', 'longitude', 'latitude')->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'addresses' => $addresses, 'deliveries' => $deliveries, "is_provider_allow_future_orders" => $provider_info->future_orders]);
	}

	public function prepareSignUp(Request $request)
	{
		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'يوجد بيانات',
				1 => 'لا يوجد بيانات',
			);
			// $city_col     = "city.city_ar_name AS city_name";
			$col = "country_ar_name AS country_name";
		} else {
			$msg = array(
				0 => 'Retrieved successfully',
				1 => 'There is no data',
			);
			// $city_col    = "city.city_en_name AS city_name";
			$col = "country_en_name AS country_name";
		}

		// $cities = DB::table('city')
		//             ->join('country', 'city.country_id', '=', 'country.country_id')
		//             ->select('city.city_id', $city_col, 'country.country_code')->get();
		$countries = DB::table('country')->select('country_id', $col, 'country_code')->get();
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'countries' => $countries]);
	}

	// public function get_user_balance(Request $request)
	// {
	//     $lang = $request->input('lang');
	//     if ($lang == "ar") {
	//         $msg = array(
	//             0 => '',
	//             1 => 'user_id مطلوب',
	//         );
	//         $canceled = ' ريال محولة من إلغاء طلب بتاريخ ';
	//         $refused = ' ريال محولة من رفض طلب بتاريخ ';
	//         $notanswered = ' ريال محولة من طلب لم يتم الرد عليه بتاريخ ';
	//         $failed = ' ريال محولة من طلب فشل توصيله بتاريخ ';
	//         $else = ' ريال محولة من مصدر غير معروف بتاريخ ';
	//     } else {
	//         $msg = array(
	//             0 => '',
	//             1 => 'user_id is required',
	//         );
	//         $canceled = ' SR from canceled order at ';
	//         $refused = ' SR from refused order at ';
	//         $notanswered = ' SR from not responded order at ';
	//         $failed = ' SR from failed to delivered order at ';
	//         $else = ' SR from unkonwn source at ';
	//     }

	//     $messages = array(
	//         'user_id.required' => 1,
	//     );

	//     $validator = Validator::make($request->all(), [
	//         'user_id' => 'required',
	//     ], $messages);

	//     if ($validator->fails()) {
	//         $error = $validator->errors()->first();
	//         return response()->json(['status' => false, 'errNum' => (int) $error, 'msg' => $msg[$error]]);
	//     }

	//     $userData = User::where('user_id', $request->input('user_id'))
	//         ->select('points', 'invitation_code')->first();

	//     if ($userData != null) {
	//         $user_balance = $userData->points;
	//         $user_code = $userData->invitation_code;
	//     } else {
	//         $user_balance = 0;
	//         $user_code = "";
	//     }

	//     //get user balance details
	//     $details = DB::table('orders_headers')->where('user_id', $request->input('user_id'))
	//         ->whereIn('status_id', [5, 6, 7, 9])
	//         ->where('payment_type', '!=', 1)
	//         ->select('total_value', DB::raw('DATE(created_at) AS day'), 'status_id AS status',
	//             DB::raw(
	//                 '(CASE status_id
	// 										  			WHEN 5 THEN CONCAT(total_value, "' . $failed . '", DATE(created_at))
	// 										  			WHEN 6 THEN CONCAT(total_value, "' . $refused . '", DATE(created_at))
	// 										  			WHEN 7 THEN CONCAT(total_value, "' . $notanswered . '", DATE(created_at))
	// 										  			WHEN 9 THEN CONCAT(total_value, "' . $canceled . '", DATE(created_at))
	// 										  			ELSE CONCAT(total_value,"' . $else . '",DATE(created_at)) END) AS full_text'
	//             )
	//         )
	//         ->get();

	//     $usedCredit = DB::table('orders_headers')->where('user_id', $request->input('user_id'))
	//         ->whereIn('status_id', [5, 6, 7, 9])
	//         ->where('payment_type', '!=', 1)
	//         ->sum('used_points');

	//     $withdrawed_balance = DB::table('withdraw_balance')->where('actor_id', $request->input('user_id'))
	//         ->where('type', 'user')
	//         ->where('status', 2)
	//         ->sum('current_balance');
	//     if ($withdrawed_balance == null || empty($withdrawed_balance)) {
	//         $withdrawed_balance = 0;
	//     }
	//     if ($user_code != "") {
	//         $invitationCredits = User::where('used_invitation_code', $user_code)->sum('invitation_credits');
	//     } else {
	//         $invitationCredits = 0;
	//     }

	//     // get user bank data
	//     $delivery_bank = DB::table("withdraw_balance")
	//         ->select("*")
	//         ->where("actor_id", $request->input("user_id"))
	//         ->where("type", "user")
	//         ->get();

	//     if (count($delivery_bank) > 0) {
	//         $last_entry = $delivery_bank[count($delivery_bank) - 1];
	//         $bank_name = $last_entry->bank_name;
	//         $bank_phone = $last_entry->phone;
	//         $bank_username = $last_entry->name;
	//         $bank_account_num = $last_entry->account_num;
	//     } else {
	//         $bank_name = "";
	//         $bank_phone = "";
	//         $bank_username = "";
	//         $bank_account_num = "";
	//     }

	//     return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'total_balance' => $user_balance, 'balance_details' => $details, 'usedCredit' => $usedCredit, 'invitationCredits' => $invitationCredits, 'withdrawed_balance' => $withdrawed_balance, "bank_name" => $bank_name, "bank_phone" => $bank_phone, "account_num" => $bank_account_num, "bank_username" => $bank_username]);
	// }

	public function getUserBalance(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => '',
				1 => 'رقم العضو مطلوب',
				2 => 'العضو غير موجود',
			);
		} else {
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'user not exists',
			);
		}

		$messages = array(
			'required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user_id = $this->get_id($request, 'users', 'user_id');

		if ($user_id == 0) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		$check = DB::table('users')->where('user_id', $user_id)->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		//get balances
		$balance = DB::table('balances')
			->where('actor_id', $user_id)
			->where('type', 'user')
			->select('current_balance')
			->first();

		if ($balance) {
			$current_balance = $balance->current_balance;
		} else {
			$current_balance = "";
		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'balance' => $current_balance]);
	}

	public function getUserInvitationCode(Request $request)
	{

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => '',
				1 => 'رقم العضو مطلوب',
				2 => 'العضو غير موجود',
				3 => 'تم جلب البيانات ',
			);
		} else {
			$msg = array(
				0 => '',
				1 => 'access_token is required',
				2 => 'user not exists',
				3 => 'data retrieved successfully',

			);
		}

		$messages = array(
			'required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user_id = $this->get_id($request, 'users', 'user_id');

		if ($user_id == 0) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		$check = DB::table('users')->where('user_id', $user_id)->first();

		if (!$check) {
			return response()->json(['status' => false, 'errNum' => 2, 'msg' => $msg[2]]);
		}

		if ($check->invitation_code) {

			return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3], 'invitationCode' => $check->invitation_code]);
		}

		$randCode = $this->getRandomString(9);

		DB::table('users')->where('user_id', $user_id)->update(['invitation_code' => $randCode]);

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[3], 'invitationCode' => $randCode]);

	}



	// public function balance_withdraw(Request $request)
	// {
	//     $lang = $request->input('lang');
	//     if ($lang == "ar") {
	//         $msg = array(
	//             0 => 'تم إضافة الطلب بنجاح',
	//             1 => 'user_id مطلوب',
	//             2 => 'المبلغ المراد سحبه مطلوب',
	//             3 => 'المبلغ المراد سحبه يجب ان يكون رقم',
	//             4 => 'إسم البنك مطلوب',
	//             5 => 'إسم صاحب الحساب مطلوب',
	//             6 => 'رقم الحساب مطلوب',
	//             7 => 'رقم الجوار مطلوب',
	//             8 => 'فشلت العلمية من فضلك حاول مره اخرى',
	//             9 => 'هناك طلب لك ما زال معلق لا يمكنك عمل الطلب حاليا',
	//             10 => 'ليس لديك رصيد كافى لاتمام هذه العملية',
	//             11 => 'الرصيد المطلوب اقل من الحد الادنى لسحب الرصيد',
	//         );
	//     } else {
	//         $msg = array(
	//             0 => 'Added successfully',
	//             1 => 'user_id is required',
	//             2 => 'value is required',
	//             3 => 'value must be a nubmer',
	//             4 => 'Bank name is required',
	//             5 => 'Name is required',
	//             6 => 'Account number is required',
	//             7 => 'Phone number is required',
	//             8 => 'Process failed, please try again',
	//             9 => 'You have a pending request, you can\'t add that request',
	//             10 => 'You do not have enough balance to execute this process',
	//             11 => 'the requested balance is less than minimum balance to withdraw',
	//         );
	//     }

	//     $messages = array(
	//         'user_id.required' => 1,
	//         'value.required' => 2,
	//         'value.numeric' => 3,
	//         'bank_name.required' => 4,
	//         'name.required' => 5,
	//         'account_num.required' => 6,
	//         'phone.required' => 7,
	//     );

	//     $validator = Validator::make($request->all(), [
	//         'user_id' => 'required',
	//         'value' => 'required|numeric',
	//         'bank_name' => 'required',
	//         'name' => 'required',
	//         'account_num' => 'required',
	//         'phone' => 'required',
	//     ], $messages);

	//     if ($validator->fails()) {
	//         $error = $validator->errors()->first();
	//         return response()->json(['status' => false, 'errNum' => (int) $error, 'msg' => $msg[$error]]);
	//     }

	//     // insert bank account data into database
	//     $actor_bank_data = DB::table("withdraw_balance")
	//         ->where("actor_id", $request->input("user_id"))
	//         ->where("type", "user")
	//         ->first();
	//     // if($actor_bank_data !== null){
	//     //     // update bank data
	//     //     DB::table("withdraw_balance")
	//     //         ->where("actor_id" , $request->input("user_id"))
	//     //         ->where("type" , "user")
	//     //         ->update([
	//     //             "name" => $request->input("name"),
	//     //             "phone" => $request->input("phone"),
	//     //             "bank_name" => $request->input("bank_name"),
	//     //             "account_num" => $request->input("account_num"),
	//     //             "updated_at" =>date('Y-m-d h:i:s')
	//     //         ]);

	//     // }else{
	//     //     // insert bank data
	//     //     DB::table("withdraw_balance")
	//     //         ->insert([
	//     //             "actor_id" => $request->input("user_id"),
	//     //             "type" => "user",
	//     //             "name" => $request->input("name"),
	//     //             "phone" => $request->input("phone"),
	//     //             "bank_name" => $request->input("bank_name"),
	//     //             "account_num" => $request->input("account_num"),
	//     //             "created_at" =>date('Y-m-d h:i:s')
	//     //         ]);
	//     // }

	//     //check if there is a pending request
	//     $check = DB::table('withdraw_balance')->where('actor_id', $request->input('user_id'))->where('type', 'user')->where('status', 1)->first();
	//     if ($check != null) {
	//         return response()->json(['status' => false, 'errNum' => 9, 'msg' => $msg[9]]);
	//     }

	//     // check if the user requested blance is avaliable
	//     $user_balace = DB::table("balances")
	//         ->select("current_balance")
	//         ->where("actor_id", $request->input("user_id"))
	//         ->where("type", "user")
	//         ->first();
	//     if ($user_balace != null) {
	//         $user_current_balance = $user_balace->current_balance;
	//     } else {
	//         return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
	//     }

	//     if ($request->input("value") > $user_current_balance) {
	//         return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);
	//     }

	//     //check if the current balance is greater than min limit of withdrawing
	//     $min_balance = DB::table("app_settings")
	//         ->select("min_balace_to_withdraw")
	//         ->first();
	//     if ($request->input("value") < $min_balance->min_balace_to_withdraw) {
	//         return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);
	//     }

	//     $check = DB::table('withdraw_balance')->insert([
	//         'actor_id' => $request->input('user_id'),
	//         'due_balance' => 0,
	//         'current_balance' => $request->input('value'),
	//         'type' => 'user',
	//         'name' => $request->input('name'),
	//         'bank_name' => $request->input('bank_name'),
	//         'account_num' => $request->input('account_num'),
	//         'phone' => $request->input('phone'),
	//     ]);

	//     if ($check) {
	//         return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
	//     } else {
	//         return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);
	//     }
	// }

	public function provider_evaluate(Request $request)
	{

		/*
        1- make validate
        2- check if order in deliveried status
        3- store rates
         */

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'تم  العملية نجاح',
				1 => 'access_token مطلوب',
				2 => 'المستخدم غير موجود ',
				3 => 'التقييم مطلوب ',
				4 => 'التقييم لابد ان سكون 1,2,3,4,5',
				5 => 'التعليق مطلوب ',
				6 => 'التعليق يجيب الا يقل عن 3 احرف ',
				7 => 'رقم الطلب مطلوب ',
				8 => 'الطلب غير موجود او ربما يكون محذوف ',
				9 => 'فشل من فضلك حاول مجداا ',
				10 => 'لابد ان  يكون الطلب في حاله تم التوصيل اولا  ولديها رقم موصل ',
				11 => 'التاجر غير موجود او ربما يكون محذوف ',
			);
		} else {
			$msg = array(
				0 => 'Added successfully',
				1 => 'Access_token is required',
				2 => 'User not found ',
				3 => 'Rates  required',
				4 => 'Rates must be in 1,2,3,4,5',
				5 => 'Comment is required',
				6 => 'Comment must be at least 3 CHAR',
				7 => 'Order_id is  required',
				8 => 'Order not exists ',
				9 => 'Faild please try again later ',
				10 => 'Order Must be in deliveried status And has delivery man  ',
				11 => 'the provider not found or may be deleted ',

			);
		}

		$messages = array(
			'access_token.required' => 1,
			'access_token.exists' => 2,
			'rates.required' => 3,
			'rates.in' => 4,
			'comment.required' => 5,
			'comment.min' => 6,
			'order_id.required' => 7,
			'order_id.exists' => 8,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required|exists:users,token',
			'comment' => 'required|min:3',
			'rates' => 'required|in:1,2,3,4,5',
			'order_id' => 'required|exists:orders_headers',

		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$user_id = $this->get_id($request, 'users', 'user_id');

		$order = DB::table('orders_headers')->where('order_id', $request->order_id)->where('user_id', $user_id)->select('status_id', 'provider_id', 'delivery_method')->first();

		if ($order) {

			//check if provider not  blocked

			$provider = DB::table('providers')->where('provider_id', $order->provider_id)->first();

			if (!$provider) {

				return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);

			}

			if ($order->status_id == 3 && $order->delivery_method == 3) // order deliveried
			{

				$inputs = [];

				$inputs['order_id'] = $request->order_id;
				$inputs['user_id'] = $user_id;
				$inputs['rates'] = $request->rates;
				$inputs['comment'] = $request->comment;
				$inputs['provider_id'] = $order->provider_id;

				DB::table('providers_rates')->insert($inputs);

			} else {

				return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);

			}

		} else {

			return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);

		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

	}

	public function delivery_evaluate(Request $request)
	{

		/*
        1- make validate
        2- check if order in deliveried status
        3- store rates
         */

		$lang = $request->input('lang');
		if ($lang == "ar") {
			$msg = array(
				0 => 'تمت العملية بنجاح ',
				1 => 'access_token مطلوب',
				2 => 'المستخدم غير موجود ',
				3 => 'التقييم مطلوب ',
				4 => 'التقييم لابد ان سكون 1,2,3,4,5',
				5 => 'التعليق مطلوب ',
				6 => 'التعليق يجيب الا يقل عن 3 احرف ',
				7 => 'رقم الطلب مطلوب ',
				8 => 'الطلب غير موجود او ربما يكون محذوف ',
				9 => 'فشل من فضلك حاول مجداا ',
				10 => 'لابد ان  يكون الطلب في حاله تم التوصيل اولا  ولديها رقم موصل ',
				11 => ' الموصل  غير موجود او ربما يكون محذوف ',
			);
		} else {
			$msg = array(
				0 => 'Added successfully',
				1 => 'Access_token is required',
				2 => 'User not found ',
				3 => 'Rates  required',
				4 => 'Rates must be in 1,2,3,4,5',
				5 => 'Comment is required',
				6 => 'Comment must be at least 3 CHAR',
				7 => 'Order_id is  required',
				8 => 'Order not exists ',
				9 => 'Faild please try again later ',
				10 => 'Order Must be in deliveried status And has delivery man  ',
				11 => 'the delivery not found or may be deleted ',

			);
		}

		$messages = array(
			'access_token.required' => 1,
			'access_token.exists' => 2,
			'rates.required' => 3,
			'rates.in' => 4,
			'comment.required' => 5,
			'comment.min' => 6,
			'order_id.required' => 7,
			'order_id.exists' => 8,
		);

		$validator = Validator::make($request->all(), [
			'access_token' => 'required|exists:users,token',
			'comment' => 'required|min:3',
			'rates' => 'required|in:1,2,3,4,5',
			'order_id' => 'required|exists:orders_headers',

		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}
		$user_id = $this->get_id($request, 'users', 'user_id');

		$order = DB::table('orders_headers')->where('order_id', $request->order_id)->where('user_id', $user_id)->select('status_id', 'provider_id', 'delivery_id', 'delivery_method')->first();

		if ($order) {

			//check if provider not  blocked

			$delivery = DB::table('deliveries')->where('delivery_id', $order->delivery_id)->first();

			if (!$delivery) {

				return response()->json(['status' => false, 'errNum' => 11, 'msg' => $msg[11]]);

			}

			if ($order->status_id == 3 && $order->delivery_method == 3) // order deliveried
			{

				$inputs = [];

				$inputs['order_id'] = $request->order_id;
				$inputs['user_id'] = $user_id;
				$inputs['rates'] = $request->rates;
				$inputs['comment'] = $request->comment;
				$inputs['delivery_id'] = $order->delivery_id;

				DB::table('delivery_rates')->insert($inputs);

			} else {

				return response()->json(['status' => false, 'errNum' => 10, 'msg' => $msg[10]]);

			}

		} else {

			return response()->json(['status' => false, 'errNum' => 8, 'msg' => $msg[8]]);

		}

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);

	}

	public function get_delivery_Methods(Request $request)
	{

		$lang = ($request->has('lang')) ? $request->lang : 'en';

		$name = ($lang == 'ar') ? 'method_ar_name' : 'method_en_name';

		$deliveryMethods = DB::table('delivery_methods')
			->select(
				'method_id AS delivery_method_id',
				DB::raw("{$name} AS method_name")
			)
			->get();

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => 'تمت العمليه بتجاح ', 'deliveryMethods' => $deliveryMethods]);

	}

	public function prepareAddOrder(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {

			$msg = array(
				0 => 'تم جلب البيانات ',
				1 => 'رقم  التاجر  غير موجود ',
				2 => 'لا يوجد بيانات',
				3 => 'المتجر غير موجود ',
			);
			$delivery_col = "delivery_methods.method_ar_name AS delivery_method";
			$payment_col = "payment_types.payment_ar_name AS payment_method";
		} else {
			$delivery_col = "delivery_methods.method_en_name AS delivery_method";
			$payment_col = "payment_types.payment_en_name AS payment_method";
			$msg = array(
				0 => 'get data successfully',
				1 => 'provider id  is required',
				2 => 'There is no data',
				3 => 'provider not found',
			);

		}

		$messages = array(
			'required' => 1,
			'exists' => 3,
		);

		$validator = Validator::make($request->all(), [
			'provider_id' => 'required|exists:providers,provider_id',
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$provider = DB::table('providers')->where('provider_id', $request->provider_id)->first();

		if (!$provider) {

			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}

		$delivery_methods = DB::table("delivery_methods")
			->join('providers_delivery_methods', 'delivery_methods.method_id', '=', 'providers_delivery_methods.delivery_method')
			->where('providers_delivery_methods.provider_id', $provider->provider_id)
			->select(
				$delivery_col,
				'delivery_methods.method_id as delivery_method_id'
			)
			->get();

		$payment_methods = DB::table("payment_types")
			->select($payment_col, 'payment_types.payment_id as payment_method_id')
			->get();

		//get app percentage
		$app_settings = DB::table('app_settings')->first();
		if ($app_settings) {
			$tax = $app_settings->tax;
		} else {
			$tax = 0;
		}

		$delivery_price = $provider->delivery_price ? $provider->delivery_price : 0;

		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0], 'delivery_methods' => $delivery_methods, 'payment_methods' => $payment_methods, 'tax' => $tax, 'delivery_price' => $delivery_price]);

	}

	public function get_checkout_id(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {

			$msg = array(
				0 => 'تم جلب البيانات',
				1 => 'رقم  التاجر غير موجود',
				2 => 'المبلغ غير صحيح',
				3 => 'المتجر غير موجود',
			);

		} else {

			$msg = array(
				0 => 'successfully done',
				1 => 'total paid amount required',
				2 => 'total paid amount is invalid',
				3 => 'failed to process',
			);

		}

		$messages = array(
			'required' => 1,
			'regex' => 2,
			'min' => 2,
		);

		$validator = Validator::make($request->all(), [
			'total_paid_amount' => array('required', 'regex:/^[0-9]{1,12}(\\.[0-9]{2})?$/', 'min:1'),
//			'total_paid_amount' => array('required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:1'),
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}

		$url = "https://test.oppwa.com/v1/checkouts";
		$data =
			"entityId=8a8294174d0595bb014d05d82e5b01d2" .
			"&amount=" . $request->total_paid_amount .
//            "&currency=SAR" .
			"&currency=EUR" .
			"&paymentType=DB" .
			"&notificationUrl=http://www.wisyst.com/notify" .
			"&merchantTransactionId=400" .
//            "&testMode=EXTERNAL".
			"&testMode=INTERNAL" .
			"&customer.email=info@wisyst.info";

		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization:Bearer OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$responseData = curl_exec($ch);
			if (curl_errno($ch)) {
				// return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
			curl_close($ch);
		} catch (\Exception $ex) {
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $ex->getMessage()]);
		}

		$id = json_decode($responseData)->id;
		return response()->json(['status' => true, 'errNum' => 0, 'checkoutId' => $id, 'msg' => $msg[0]]);


		/*$url = "https://test.oppwa.com/v1/checkouts";
		$data =
			"entityId=8a8294174d0595bb014d05d82e5b01d2" .
			"&amount=" . $request->total_paid_amount .
			"&currency=SAR" .
			"&paymentType=DB" .
            "&testMode=EXTERNAL" .
			"&notificationUrl=http://localhost/storemapv2/public/api/notify_payment";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization:Bearer OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if (curl_errno($ch)) {
			return response()->json();

			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}
		curl_close($ch);

		$id = json_decode($responseData)->id;

		return response()->json(['status' => true, 'errNum' => 0, 'checkoutId' => $id, 'msg' => $msg[0]]);*/
	}

	public function check_status(Request $request)
	{

		$lang = $request->input('lang');

		if ($lang == "ar") {

			$msg = array(
				0 => 'تم جلب البيانات ',
				1 => 'رقم التاجر غير موجود ',
				2 => 'لا يوجد بيانات',
				3 => 'المتجر غير موجود ',
			);

		} else {

			$msg = array(
				0 => 'successfully done',
				1 => 'checkout id required',
				3 => 'faild to process ',
			);

		}

		$messages = array(
			'required' => 1,
		);

		$validator = Validator::make($request->all(), [
			'checkoutId' => array('required'),
		], $messages);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json(['status' => false, 'errNum' => (int)$error, 'msg' => $msg[$error]]);
		}


		$url = "https://test.oppwa.com/v1/checkouts/{$request->checkoutId}/payment";
		$url .= "?entityId=8a8294174d0595bb014d05d82e5b01d2";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization:Bearer OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if (curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		$r = json_decode($responseData);
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $r->result->description, 'code' => $r->result->code]);


		/*$url = "https://test.oppwa.com/v1/checkouts/{$request->checkoutId}/payment";
		$url .= "?entityId=8a8294174d0595bb014d05d82e5b01d2";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization:Bearer OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if (curl_errno($ch)) {
			return curl_error($ch);

		}
		curl_close($ch);

		$r = json_decode($responseData);
		return response()->json(['status' => true, 'errNum' => 0, 'msg' => $r->result->description]);*/


		/*if ($r->result->description == "Request successfully processed in 'Merchant in Connector Test Mode'") {
			// make order
			if (1 == 2) {
				//Session::flash('success', $mgs[0]);

				return response()->json(['status' => true, 'errNum' => 0, 'msg' => $msg[0]]);
			} else {
				return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
			}
		} else {
			// Error in payment
			return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
		}*/

	}

}
