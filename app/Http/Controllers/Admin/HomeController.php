<?php

namespace App\Http\Controllers\Admin;

/**
 * Class ProviderController.
 * it is a class to manage all provider functionalities
 *  provider sign up
 *  provider log in
 *  provider forget password
 * ..etc.
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
use App\Product;
use App\Deliveries;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class HomeController extends Controller
{
	public function __construct(){
		
	}

	public function index(){
		$data['activeproviders']    = Providers::where('publish',1)   -> count();
		$data['inactiveproviders']  = Providers::where('publish',0)   -> count();
		$data['activedeliveries']   = Deliveries::where('publish',1)  -> count();
		$data['inactivedeliveries'] = Deliveries::where('publish',0)  -> count();


		$data['activeusers']        = User::where('status',1) -> count();
		$data['inactiveusers']      = User::where('status',0) -> count();
		$data['comments']           = DB::table('product_comments')->where('is_read',0) -> count();
		$data['sale']               = DB::table('orders_headers')->where('status_id', 3)->
		                select(DB::raw('IFNULL((app_value + delivery_app_value),0) AS total'))
		                ->first();
		$data['sale']   =  ($data['sale'])? $data['sale']->total : 0;
		$data['return'] = DB::table('orders_headers')->where('status_id', '!=', 4)
											 ->where('payment_type', '!=', 1)
											 ->select(DB::raw('IFNULL((app_value + delivery_app_value),0) AS total'))->first();
		$data['return']    = ($data['return'] != NULL)? $data['return']->total : 0;
		$data['products']  = Product::where('publish', 1)->count();

	 	$data['offers']    = DB::table('providers_offers') -> where('status',0) ->where('providers_offers.expire', 0) -> count();

		$data['excellentReq']      = DB::table('excellence_requests') -> where('excellence_requests.paid', '0') -> where('excellence_requests.publish', '0') -> where('status',0)  -> count();

		return view('cpanel.home',$data);
	}
}
