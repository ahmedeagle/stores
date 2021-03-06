<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\User;
use App\Categories;
use App\Providers;
use App\Meals;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

class OrdersController extends Controller
{
	public function __construct(){
		
	}

	public function getAllOrders(){
		$details = array();
		$header = DB::table('orders_headers')
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user')
					->orderBy('orders_headers.created_at', 'DESC')
					->get();

		if(empty($headers)){
			return false;
		}

		foreach($headers AS $header){
			$details[] = DB::table('order_details')
						   ->where('order_details.order_id', $header->order_id)
						   ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
						   ->select('order_details.*', 'meals.meal_name', 'meals.main_image')
						   ->get();
		}

		$result = array(
			'headers'  => $headers,
			'details' => $details
		);

		return $result;
	}
	public function getOrdersById($id){
		$header = DB::table('orders_headers')
					->where('orders_headers.order_id', $id)
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
					->first();

		if($header == NULL){
			return false;
		}

		$details = DB::table('order_products')
					 ->where('order_products.order_id', $header->order_id)
					 ->join('products', 'order_products.product_id', '=', 'products.id')
					 ->select('order_products.*', 'products.title')
					 ->get();


					 if(isset($details) && $details -> count() > 0){
                
			                foreach ($details as  $product) {
			                	  
			                     $image = DB::table('product_images') -> where('product_id',$product -> product_id) -> first();

			                     if($image){

			                     	 $product -> main_image =  env('APP_URL').'/public/products/'.$image -> image;

			                     }else{
			                      
			                         $product -> main_image ="";

			                     }
			                }
 
						} 


		$result = array(
			'header'        => $header,
			'details'       => $details,
 		);

		return $result;

	}

	public function getOrdersByCode($code){
		$header = DB::table('orders_headers')
					->where('orders_headers.order_code', $code)
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
					->first();

		if($header != NULL){
			return false;
		}

		$details = DB::table('order_details')
					 ->where('order_details.order_id', $header->order_id)
					 ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
					 ->select('order_details.*', 'meals.meal_name', 'meals.main_image')
					 ->get();

		$result = array(
			'header'  => $header,
			'details' => $details
		);

		return $result;
	}

	public function getOrdersByProvider($provider){
		$details = array();
		$header = DB::table('orders_headers')
					->where('orders_headers.provider_id', $provider)
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
					->get();

		if(empty($headers)){
			return false;
		}

		foreach($headers AS $header){
			$details[] = DB::table('order_details')
						   ->where('order_details.order_id', $header->order_id)
						   ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
						   ->select('order_details.*', 'meals.meal_name', 'meals.main_image')
						   ->get();
		}

		$result = array(
			'headers'  => $headers,
			'details' => $details
		);

		return $result;

	}

	public function getOrdersByDelivery($delivery){
		$details = array();
		$header = DB::table('orders_headers')
					->where('orders_headers.delivery_id', $delivery)
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
					->get();

		if(empty($headers)){
			return false;
		}

		foreach($headers AS $header){
			$details[] = DB::table('order_details')
						   ->where('order_details.order_id', $header->order_id)
						   ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
						   ->select('order_details.*', 'meals.meal_name', 'meals.main_image')
						   ->get();
		}

		$result = array(
			'headers'  => $headers,
			'details' => $details
		);

		return $result;
	}

	public function getOrdesByUser($user){
		$details = array();
		$header = DB::table('orders_headers')
					->where('orders_headers.user_id', $user)
					->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
					->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
					->join('users', 'orders_headers.user_id', '=', 'users.user_id')
					->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
					->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
					->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
					->get();

		if(empty($headers)){
			return false;
		}

		foreach($headers AS $header){
			$details[] = DB::table('order_details')
						   ->where('order_details.order_id', $header->order_id)
						   ->join('meals', 'order_details.meal_id', '=', 'meals.meal_id')
						   ->select('order_details.*', 'meals.meal_name', 'meals.main_image')
						   ->get();
		}

		$result = array(
			'headers'  => $headers,
			'details' => $details
		);

		return $result;
	}

	public function getOrdersHeaders(){
		return $header = DB::table('orders_headers')
							->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
							->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
							->join('users', 'orders_headers.user_id', '=', 'users.user_id')
							->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
							->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
							->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name')
							->orderBy('orders_headers.created_at', 'DESC')
							->get();
	}

	public function getOrdersFilter(){
		$headers = $this->getOrdersHeaders();
		return view('cpanel.orders.headers', compact('headers'));
	}

	

	public function getSales(){
		$headers = DB::table('orders_headers')
		                    ->where('orders_headers.status_id',3)
		                    ->where('orders_headers.expired',0)
							->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
							->leftjoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
							->join('users', 'orders_headers.user_id', '=', 'users.user_id')
							->join('order_status', 'orders_headers.status_id', '=', 'order_status.status_id')
							->join('delivery_methods', 'orders_headers.delivery_method', '=', 'delivery_methods.method_id')
							->select('orders_headers.*', 'providers.full_name AS provider', DB::raw('IFNULL(deliveries.full_name, "") AS delivery'), 'users.full_name AS user', 'order_status.en_desc AS sts', 'delivery_methods.method_en_name',
								'orders_headers.app_percentage',
								'orders_headers.app_value'

						         )
							->orderBy('orders_headers.created_at', 'DESC')
							->get();

		return view('cpanel.orders.headers', compact('headers'));
	}

	public function getOrderDetails($id, Request $request){
		$order = $this->getOrdersById($id);
		if($order == false){
			$request->session()->flash('error', 'Invalid requested order');
			return redirect()->route('orders.filter');
		}

		  $orderOptions = DB::table('order_products_options') -> where('order_id',$id) -> join('product_options','order_products_options.option_id','=','product_options.id') -> select('product_options.name','product_options.id','product_options.price')  -> get();

		$header  = $order['header'];
		$details = $order['details'];

		return view('cpanel.orders.details', compact('header', 'details','orderOptions'));
	}
}
