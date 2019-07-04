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

class SettingController extends Controller
{
	public function __construct(){
		
	}

	public function getSetting()
	{
		$setting = DB::table('app_settings')->first();
		return view('cpanel.setting.setting', compact('setting'));
	}

	public function saveSetting(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'hours'     => 'required|numeric',
			'minutes'   => 'required|numeric',
			'delivery'  => 'required|numeric',
 			'provider'  => 'required|numeric',
			'type'      => 'required|numeric',
			'inPoints'  => 'required|numeric',
			'outPoints' => 'required|numeric',
			'kilo' => 'required|numeric',
            'min_balance' => 'required|numeric',
            'delivery_price_outside' => 'required|numeric',
            'offer_day_coast'        => 'required|numeric',
            'excellence_day_coast'   => 'required|numeric'
            
		]);

		if($validator->fails()){
			return redirect()->back()->withErrors($validator->errors())->withInputs();
		}

		$insert = DB::table('app_settings')
			      ->insert([
			     		'time_in_hours'              => $request->input('hours'),
			     		'time_in_min'                => $request->input('minutes'),
			     		'app_percentage'             => $request->input('provider'),
			     		'delivery_percentage'        => $request->input('delivery'),
 			     		'invitation_type'            => $request->input('type'),
			     		'inviter_points'             => $request->input('inPoints'),
			     		'invited_points'             => $request->input('outPoints'),
			     		'kilo_price'                 => $request->input('kilo'),
                        'min_balace_to_withdraw'     => $request->input('min_balance'),
                         'delivery_price_outside'     => $request->input('delivery_price_outside'),
                         'offer_day_coast'            => $request->input('offer_day_coast'),
                         'excellence_day_coast'       => $request->input('excellence_day_coast')
			     ]);
		if($insert){
			$request->session()->flash('success', 'Setting has added successfully');
			return redirect()->route('setting.show');
		}else{
			$errors = array('Failed to add the setting, please try again later');
			return redirect()->back()->withErrors($errors)->withInputs();
		}
	}

	public function updateSetting(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id'        => 'required|numeric',
			'hours'     => 'required|numeric',
			'minutes'   => 'required|numeric',
			'delivery'  => 'required|numeric',
 			'provider'  => 'required|numeric',
			'type'      => 'required|numeric',
			'inPoints'  => 'required|numeric',
			'outPoints' => 'required|numeric',
			'kilo'      => 'required|numeric',
            'min_balance' => 'required|numeric',
              'delivery_price_outside'   => 'required|numeric',
              'offer_day_coast'          => 'required|numeric',
              'excellence_day_coast'     => 'required|numeric'
		]);



			if($validator->fails()){
				return redirect()->back()->withErrors($validator->errors())->withInput();
			}
 


		$update = DB::table('app_settings')
					  ->where('id', $request->input('id'))
				      ->update([
				     		'time_in_hours' 	       => $request->input('hours'),
				     		'time_in_min'  		       => $request->input('minutes'),
				     		'app_percentage' 	       => $request->input('provider'),
				     		'delivery_percentage'      => $request->input('delivery'),
				     		'invitation_type'          => $request->input('type'),
				     		'inviter_points'           => $request->input('inPoints'),
				     		'invited_points'           => $request->input('outPoints'),
				     		'kilo_price'               => $request->input('kilo'),
	                        'min_balace_to_withdraw'    => $request->input('min_balance'),
	                        'delivery_price_outside'     => $request->input('delivery_price_outside'),
	                         'offer_day_coast'            => $request->input('offer_day_coast'),
	                         'excellence_day_coast'       => $request->input('excellence_day_coast')
 
				     ]);
	  
			if($update){
				$request->session()->flash('success', 'Updated successfully');
				return redirect()->route('setting.show');
			}else{
				$errors = array('Failed to update, please try again later or no changes added ');
				return redirect()->back()->withErrors($errors)->withInput();
			}




	}



}
