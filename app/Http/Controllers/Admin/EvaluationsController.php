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

class EvaluationsController extends Controller
{
	public function __construct(){
		
	}

	public function getEvaluations(){
		$users = DB::table('users')->select('user_id', 'full_name')->get();
		$evaluations = DB::table('evaluation')
					   ->join('users', 'evaluation.user_id', '=', 'users.user_id')
					   ->join('orders_headers', 'evaluation.order_id', '=', 'orders_headers.order_id')
					   ->select(
					   		'evaluation.id',
					   		'users.full_name',
					   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
					   		'users.profile_pic',
					   		'orders_headers.order_code AS code',
					   		'evaluation.comment',
					   		DB::raw('((evaluation.quality + evaluation.autotype + evaluation.packing + evaluation.maturity + evaluation.ask_again + evaluation.delivery_arrival + evaluation.delivery_in_time + evaluation.delivery_attitude) / 8) AS rating'),
					   		DB::raw('DATE(evaluation.created_at) AS created')
					   	)->paginate(40);
		return view('cpanel.evaluation.evaluations', compact('evaluations', 'users'));
	}


	public function getProviderEvaluations(){
		$users = DB::table('users')->select('user_id', 'full_name')->get();
		$providers = Providers::select('provider_id', 'full_name')->get();
		$evaluations = DB::table('providers_rates')
					   ->join('users', 'providers_rates.user_id', '=', 'users.user_id')
					   ->join('orders_headers', 'providers_rates.order_id', '=', 'orders_headers.order_id')
					   ->join('providers', 'providers_rates.provider_id', '=', 'providers.provider_id')
					   ->select(
					   		'providers_rates.id',
					   		'users.full_name',
					   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
					   		'users.profile_pic',
					   		'providers.full_name AS provider_name',
 					   		 DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/', providers.profile_pic) AS provider_pic"),
					   		DB::raw('CONCAT(providers.country_code, providers.phone) AS provider_phone'),
					   		'orders_headers.order_code AS code',
					   		'providers_rates.comment',
					   		DB::raw('(providers_rates.rates) AS rating'),
					   		DB::raw('DATE(providers_rates.created_at) AS created')
					   	)->paginate(40);
		return view('cpanel.evaluation.provider_evaluations', compact('evaluations', 'users', 'providers'));
	}

	public function getDeliveryEvaluations(){
		$users = DB::table('users')->select('user_id', 'full_name')->get();
		$deliveries = DB::table('deliveries')->select('delivery_id', 'full_name')->get();
		$evaluations = DB::table('delivery_evaluation')
					   ->join('users', 'delivery_evaluation.user_id', '=', 'users.user_id')
					   ->join('orders_headers', 'delivery_evaluation.order_id', '=', 'orders_headers.order_id')
					   ->join('deliveries', 'delivery_evaluation.delivery_id', '=', 'deliveries.delivery_id')
					   ->select(
					   		'delivery_evaluation.id',
					   		'users.full_name',
					   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
 					   		'deliveries.full_name AS delivery_name',
 					   		DB::raw('CONCAT(deliveries.country_code, deliveries.phone) AS delivery_phone'),
					   		'orders_headers.order_code AS code',
					   		'delivery_evaluation.comment',
					   		DB::raw('((delivery_evaluation.delivery_arrival  + delivery_evaluation.delivery_in_time + delivery_evaluation.delivery_attitude) / 3) AS rating'),
					   		DB::raw('DATE(delivery_evaluation.created_at) AS created')
					   	)->paginate(40);
		return view('cpanel.evaluation.delivery_evaluations', compact('evaluations', 'users', 'deliveries'));
	}

	// public function evaluationDetails($id){
	// 	$details = DB::table('evaluation')
	// 				   ->join('users', 'evaluation.user_id', '=', 'users.user_id')
	// 				   ->join('orders_headers', 'evaluation.order_id', '=', 'orders_headers.order_id')
	// 				   ->join('providers', 'orders_headers.provider_id', '=', 'providers.provider_id')
	// 				   ->leftJoin('deliveries', 'orders_headers.delivery_id', '=', 'deliveries.delivery_id')
	// 				   ->select(
	// 				   		'users.full_name',
	// 				   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
	// 				   		'users.profile_pic',
	// 				   		'providers.full_name AS provider',
	// 				   		DB::raw('CONCAT(providers.country_code, providers.phone) AS provider_phone'),
	// 				   		'providers.profile_pic AS provider_pic',
	// 				   		DB::raw('IFNULL(deliveries.full_name, "") AS delivery'),
	// 				   		DB::raw('IFNULL(CONCAT(deliveries.country_code, deliveries.phone), "") AS delivery_phone'),
	// 				   		DB::raw('IFNULL(deliveries.profile_pic, "") AS delivery_pic'),
	// 				   		'orders_headers.order_code AS code',
	// 				   		'evaluation.*',
	// 				   		DB::raw('DATE(evaluation.created_at) AS created')
	// 				   	)->first();
	// 	return view('cpanel.evaluation.details', compact('details'));
	// }

	public function evaluationSearch(Request $request){

 		$string        = '';
		$user          = $request->input('user');
		$user_phone    = $request->input('user_phone');
		$subject       = $request->input('subject');
		$subject_phone = $request->input('subject_phone');
		$from          = $request->input('from');
		$to            = $request->input('to');
		$type 		   = $request->input('type');

		if($type == 'provider'){
			$table   = 'providers_rates';
			$joinTbl = 'providers';
			$col     = 'providers_rates.provider_id';
			$joinCol = 'providers.provider_id';
			$rate    = '(rates) AS rating';
		}else{
			$table   = 'delivery_rates';
			$joinTbl = 'deliveries';
			$col     = 'delivery_rates.delivery_id';
			$joinCol = 'deliveries.delivery_id';
			$rate    = '(rates) AS rating';
		}

		$conditions = [];

		if(!empty($user)){
			$conditions[] = [$table.'.user_id', '=', $user];
		}

		if(!empty($subject)){
			$conditions[] = [$col, '=', $subject];
		}

		if(!empty($user_phone)){
			$conditions[] = [DB::raw('users.phone = "'.$user_phone.'" OR CONCAT(users.country_code, users.phone) = "'.$user_phone.'"')];
		}

		if(!empty($subject_phone)){
			$conditions[] = [DB::raw($joinTbl.'.phone = "'.$subject_phone.'" OR CONCAT('.$joinTbl.'.country_code, '.$joinTbl.'.phone) = "'.$subject_phone.'"')];
		}

		if(!empty($from)){
			$conditions[] = [DB::raw('DATE('.$table.'.created_at'), '>=', $from];
		}

		if(!empty($to)){
			$conditions[] = [DB::raw('DATE('.$table.'.created_at'), '<=', $to];
		}

		if(!empty($conditions)){
			$evaluations = DB::table($table)
							  ->where($conditions)
							  ->join('users', $table.'.user_id', '=', 'users.user_id')
							  ->join($joinTbl, $col, '=', $joinCol)
							  ->join('orders_headers', $table.'.order_id', '=', 'orders_headers.order_id')
							  ->select(
								  	$table.'.id',
							   		'users.full_name',
							   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
 							   		$joinTbl.'.full_name AS name',
							   		$joinTbl.'.profile_pic AS pic',
							   		DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',".$joinTbl.".profile_pic) AS pic"),
							   		DB::raw('CONCAT('.$joinTbl.'.country_code, '.$joinTbl.'.phone) AS subject_phone'),
							   		'orders_headers.order_code AS code',
							   		$table.'.comment',
							   		DB::raw($rate),
							   		DB::raw('DATE('.$table.'.created_at) AS created')
							  	)->get();
		}else{
			$evaluations = DB::table($table)
							  ->join('users', $table.'.user_id', '=', 'users.user_id')
							  ->join($joinTbl, $col, '=', $joinCol)
							  ->join('orders_headers', $table.'.order_id', '=', 'orders_headers.order_id')
							  ->select(
								  	$table.'.id',
							   		'users.full_name',
							   		DB::raw('CONCAT(users.country_code, users.phone) AS phone'),
 							   		$joinTbl.'.full_name AS name',
							   		DB::raw("CONCAT('".env('APP_URL')."','/public/providerProfileImages/',".$joinTbl.".profile_pic) AS pic"),
							   		DB::raw('CONCAT('.$joinTbl.'.country_code, '.$joinTbl.'.phone) AS subject_phone'),
							   		'orders_headers.order_code AS code',
							   		$table.'.comment',
							   		DB::raw($rate),
							   		DB::raw('DATE('.$table.'.created_at) AS created')
							  	)->get();
		}

		if($evaluations->count()){
			foreach($evaluations AS $evaluation){
				$string .= '<tr>
                                
                                <td>'.$evaluation->full_name.'</td>
                                <td>'.$evaluation->phone.'</td>
                                <td class="width-90">
                                    <a class="img-popup-link" href="'.$evaluation->pic.'">
                                        <img src="'.$evaluation->pic.'" class="table-img">
                                    </a>

                                </td>
                                <td>'.$evaluation->name.'</td>
                                <td>'.$evaluation->subject_phone.'</td>
                                <td>'.$evaluation->code.'</td>
                                <td>'.$evaluation->rating.'</td>
                                <td>'.$evaluation->comment.'</td>
                                <td>'.$evaluation->created.'</td>
                            </tr>';
			}
		}else{
			$string = '<tr><td colspan="10">لا يوجد نتائج</td>';
		}

		return $string;
	}
}
