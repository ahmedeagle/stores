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
use App\Deliveries;
 use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Storage;
 
class DeliveryController extends Controller
{
	public function __construct(){
		
	}

	public function getDeliveryIncomeView(){
		$deliveries = Deliveries::get();
		return view('cpanel.deliveries.income', compact('deliveries'));
	}

	public function create(){
		//get countries
		$countries = DB::table('country')->where('publish', 1)->get();
		//get categories
		$categories = DB::table('categories')->where('publish', 1)->get();
		return view('cpanel.deliveries.create', compact('countries', 'categories'));
	}

	 
	 protected function checkCountryCodeFormate($str){
         
               if(mb_substr(trim($str), 0, 1) === '+'){                         
                          return  $str;                     
                  }
                    
                  return '+'.$str;                  
    }

	public function show(){
		$deliveries = Deliveries::join('city', 'deliveries.city_id', '=','city.city_id')
							    ->join('country', 'deliveries.country_id', '=','country.country_id')
							    ->select('deliveries.*', DB::raw('country.country_ar_name AS country'), DB::raw('city.city_ar_name AS city'))
							    ->get();
		return view('cpanel.deliveries.deliveries', compact('deliveries'));
	}

	public function store(Request $request){
		
       
	    $messages = [
            'full_name.required'                  => 'لابد من ادخال الاسم  بالكامل ',
 	          'phone.required'                    => 'رقم الجوال مطلوب ولا ',
            'phone.unique'                        => 'رقم الجوال مستخدم من قبل',
            'phone.regex'                         => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
             'country_code.required'              => 'لابد من ادحال كود الدوله ',
            'password_confirmation.required'      => 'لابد من تاكيد  كلمة  المرور ',
            'password.required'                   => 'لابد من ادحال كلمة المرور ',
            'password.min'                        => 'لابد الا تقل كلمه المرور عن 8 احرف ',
            'password.confirmed'                  => 'لابد من تاكيد كلمه المرور ',
            'country_id.required'                 => 'لابد من  اختيار دوله ',
            'country_id.exists'                   => 'الدوله غير موجوده او ربما  تكون محذوفه ',
             'city_id.required'                   => 'لايد من اختيار المدينة ',
            'city_id.exists'                      => 'المدينه المحتاره غير موجوده او ربما قد حذفت ',
             'longitude.required'                 => 'لابد من تحديد مكانك علي الحريطة ',
            'latitude.required'                   => 'لابد من تحديد مكانك علي الخريطه ',
            'license_img.required'                => 'لابد من رفع صوره الرخصه للمركبه ',
            'car_form_img.required'               => 'لابد من رفع  صوره استماره السياره ',
            'Insurance_img.required'              => 'للابد من رفع صوره التامين',
            'national_img.required'               => 'لابد من رفع صوره بطاقه الرقم القومي ',
             'mimes'                              => 'لابد ان تكون الصوره من نوع  jpg or png ',

        ];


        $rules=[

            'full_name'              => 'required',
            'car_number'             => 'required',
            'phone'                  =>  array('required','unique:deliveries,phone','regex:/^(05|5)([0-9]{8})$/'),
            'country_code'           => 'required',
            'password_confirmation'  => 'required',
            'password'               => 'required|min:8|confirmed',         
            'country_id'             => 'required|exists:country,country_id',
            'city_id'                => 'required|exists:city,city_id',
            'longitude'              => 'required',
            'latitude'               => 'required',
            'license_img'            => 'required|mimes:jpeg,png',
            'car_form_img'           => 'required|mimes:jpeg,png',
            'Insurance_img'          => 'required|mimes:jpeg,png',
             'national_img'           => 'required|mimes:jpeg,png',
        ];


      	if($request->hasFile('authorization_img')){
               //rules['authorization_img'] = 'mimes';
      	}
			  
      $validator = Validator::make($request->all(),$rules, $messages);
  

        if($validator->fails()){


               return redirect()->back()->with('errors', $validator->errors())->withInput();
        }

       
          $inputs = $request -> only('full_name','phone','country_id','city_id','car_number','longitude','latitude');

           $inputs['country_code']        =  $this -> checkCountryCodeFormate($request->input('country_code'));
           $inputs['password']            =  md5($request -> password);
           $inputs['status']              = 1;
           $inputs['publish']             = 1;           
           $inputs['account_activated']   = 1;
           $inputs['token']               = $this -> getRandomString(128);

              if($request -> hasFile('license_img')){
                        $image  = $request -> license_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['license_img'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['license_img'] = "avatar_ic.png";
                    }

                     if($request -> hasFile('car_form_img')){
                        $image  = $request -> car_form_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['car_form_img'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['car_form_img'] = "avatar_ic.png";
                    }
 

                  if($request -> hasFile('Insurance_img')){
                        $image  = $request -> Insurance_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['Insurance_img'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['Insurance_img'] = "avatar_ic.png";
                    }
 


                  if($request -> hasFile('authorization_img')){
                        $image  = $request -> authorization_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['authorization_img'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['authorization_img'] = "avatar_ic.png";
                    }
 

                if($request -> hasFile('national_img')){
                        $image  = $request -> national_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['national_img'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['national_img'] = "avatar_ic.png";
                    }


					  try{

                           $id = Deliveries::insertGetId($inputs);

					  }catch(Exception $e){

                           
                           $request->session()->flash('faild', 'فشل رجاء المحاوله مجددا ');
                             
					  }
                 

                   $request->session()->flash('success', 'تم الاضافه بنجاح ');
			       return redirect()->route('deliveries.show');		 
                   
	}

	public function edit($id){
	 

         $data =[];
		//get Provider data 
		$data['delivery']      = Deliveries::where('delivery_id',$id) -> first();


        if(!$data['delivery']){

             return abort('404');
        }

		    $city_id       = $data['delivery']->city_id;
		    $country_id    = $data['delivery']->country_id;
 
     
		//get countries
		 $data['countries']  = DB::table('country')-> where('publish',1) 
                                         -> select('country_id',
                                                  'country_ar_name',
                                                  'country_code',
                                                  DB::raw('IF(country_id = '.$country_id.', true, false) AS choosen')
                                                   )-> get();

   
		//get country cities
		  $data['cities']       = DB::table('city')->select('city_id','city_ar_name','country_id',DB::raw('IF(city_id = '.$city_id.', true, false) AS choosen'))->get();
 

		return view('cpanel.deliveries.edit',$data);
	}

	public function update(Request $request){


	    $messages = [
            'full_name.required'                  => 'لابد من ادخال الاسم  بالكامل ',
 	          'phone.required'                    => 'رقم الجوال مطلوب ولا ',
            'phone.unique'                        => 'رقم الجوال مستخدم من قبل',
            'phone.regex'                         => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
             'country_code.required'              => 'لابد من ادحال كود الدوله ',
            'password_confirmation.required'      => 'لابد من تاكيد  كلمة  المرور ',
            'password.required'                   => 'لابد من ادحال كلمة المرور ',
            'password.min'                        => 'لابد الا تقل كلمه المرور عن 8 احرف ',
            'password.confirmed'                  => 'لابد من تاكيد كلمه المرور ',
            'country_id.required'                 => 'لابد من  اختيار دوله ',
            'country_id.exists'                   => 'الدوله غير موجوده او ربما  تكون محذوفه ',
             'city_id.required'                   => 'لايد من اختيار المدينة ',
            'city_id.exists'                      => 'المدينه المحتاره غير موجوده او ربما قد حذفت ',
             'longitude.required'                 => 'لابد من تحديد مكانك علي الحريطة ',
            'latitude.required'                   => 'لابد من تحديد مكانك علي الخريطه ',
              'mimes'                              => 'لابد ان تكون الصوره من نوع  jpg or png ',
              'delivery_id.required'               => 'رقم الموصل مطلوب ',
              'delivery_id.exists'                 => 'الموصل غير موجود ', 

        ];


        $rules=[

            'full_name'              => 'required',
            'car_number'             => 'required',
            'phone'                  =>  array('required','unique:deliveries,phone,'.$request->input('delivery_id').',delivery_id','regex:/^(05|5)([0-9]{8})$/'),
            'country_code'           => 'required',
              'country_id'             => 'required|exists:country,country_id',
            'city_id'                => 'required|exists:city,city_id',
            'longitude'              => 'required',
            'latitude'               => 'required',
            'delivery_id'            => 'required|exists:deliveries,delivery_id'
         ];


			   if($request -> hasFile('license_img')){

			      $rules['license_img'] = 'required|mimes:jpeg,png';
			 
			    }

			     if($request -> hasFile('car_form_img')){

			      $rules['car_form_img'] = 'required|mimes:jpeg,png';
			 
			    }

			    if($request -> hasFile('Insurance_img')){

			      $rules['Insurance_img'] = 'required|mimes:jpeg,png';
			 
			    }

			    if($request -> hasFile('authorization_img')){

			      $rules['authorization_img'] = 'required|mimes:jpeg,png';
			 
			    }
			     if($request -> hasFile('national_img')){

			      $rules['national_img'] = 'required|mimes:jpeg,png';
			 
			    }

			     if($request -> hasFile('national_img')){

			      $rules['national_img'] = 'required|mimes:jpeg,png';
			 
			    }
			 
			    
			    if($request ->has('password')){ 
			          $rules['password_confirmation'] = 'required';
			          $rules['password']              = 'required|min:8|confirmed';
			      }

      $validator = Validator::make($request->all(),$rules, $messages);
 
 
        if($validator->fails()){

              // return redirect()->back()->with('errors', $validator->errors())->withInput();
        }


      
          $inputs = $request -> only('full_name','phone','country_id','city_id','car_number','longitude','latitude');

           if($request -> has('password')){
                   
                     $inputs['password']            =  md5($request -> password);
                 } 


           $inputs['country_code']        =  $this -> checkCountryCodeFormate($request->input('country_code'));
           $inputs['status']              = 1;
           $inputs['publish']             = 1;           
           $inputs['account_activated']   = 1;


             $images = array();


                 if($request -> hasFile('license_img')){


                      //delete the previous image from storage 
                       if(Storage::disk('deliveries')->exists($request -> input('license_img')))
                       {
                             
                            Storage::disk('deliveries')->delete($request -> input('license_img'));

                       }
 
                         $image  = $request -> license_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['license_img'] =  $nameOfImage;                           
                 }

                  if($request -> hasFile('national_img')){

 
                      //delete the previous image from storage 
                       if(Storage::disk('deliveries')->exists($request -> input('national_img')))
                       {
                             
                            Storage::disk('deliveries')->delete($request -> input('national_img'));

                       }
 
                         $image  = $request -> national_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['national_img'] =  $nameOfImage;                           
                 }

                  if($request -> hasFile('authorization_img')){


                      //delete the previous image from storage 
                       if(Storage::disk('deliveries')->exists($request -> input('authorization_img')))
                       {
                             
                            Storage::disk('deliveries')->delete($request -> input('authorization_img'));

                       }
 
                         $image  = $request -> authorization_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['authorization_img'] =  $nameOfImage;                           
                 }


                  if($request -> hasFile('Insurance_img')){


                      //delete the previous image from storage 
                       if(Storage::disk('deliveries')->exists($request -> input('Insurance_img')))
                       {
                             
                            Storage::disk('deliveries')->delete($request -> input('Insurance_img'));

                       }
 
                         $image  = $request -> Insurance_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['Insurance_img'] =  $nameOfImage;                           
                 }


                  if($request -> hasFile('car_form_img')){


                      //delete the previous image from storage 
                       if(Storage::disk('deliveries')->exists($request -> input('car_form_img')))
                       {
                             
                            Storage::disk('deliveries')->delete($request -> input('car_form_img'));

                       }
 
                         $image  = $request -> car_form_img ;
                        //save new image   
                        $image ->store('/','deliveries');
                        $nameOfImage = $image ->hashName();
                        $inputs['car_form_img'] =  $nameOfImage;                           
                 }



            try {

            	 Deliveries::where('delivery_id',$request -> delivery) -> update($inputs);
             	
             } catch (Exception $e) {
             	
                    $request->session()->flash('faild', 'فشل رجاء المحاوله مجددا ');
             } 
 

           $request->session()->flash('success', 'تم التعديل  بنجاح ');
	       return redirect()->route('deliveries.show');		 
         
	}

	// function to activate delivery
    public function activateDelivery($id , Request $request){

	    $delivery_info =  DB::table("deliveries")
                            ->where("delivery_id" , $id)
                            ->select("status")
                            ->first();
	    if($delivery_info->status == 0 || $delivery_info->status == "0"){
	        $update = 3;
        }else{
            $update = 1;
        }
        date_default_timezone_set('Asia/Riyadh');
        $timestamp =  date("Y/m/d H:i:s", time());
        DB::table("deliveries")
            ->where("delivery_id" , $id)
            ->update(["status" => $update, "admin_activation_time" => $timestamp]);
        $request->session()->flash('success', 'Delivery has been activated successfully');
        return redirect()->back()->withInput();
    }
}
