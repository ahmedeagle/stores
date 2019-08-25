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
 use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;
use Storage;

class ProviderController extends Controller
{
	public function __construct(){
		
	}

	public function show(){


    $request = Request();


    $status =  $request -> status;

    if(in_array($status, ['active','inactive'])){

                  $cond = ($status == 'active') ? 1 : 0 ; 
                  $conditions[]=['providers.publish','=',$cond];
    }


if(!empty($conditions)){

              
    $providers = Providers::where($conditions) 
                 -> join('city', 'providers.city_id', '=','city.city_id')
                ->join('country', 'providers.country_id', '=','country.country_id')
                ->select('providers.*', DB::raw('country.country_en_name AS country'), DB::raw('city.city_en_name AS city'))
                ->get();



    }else{

    $providers = Providers::join('city', 'providers.city_id', '=','city.city_id')
                ->join('country', 'providers.country_id', '=','country.country_id')
                ->select('providers.*', DB::raw('country.country_en_name AS country'), DB::raw('city.city_en_name AS city'))
                ->get();

    }
 

                            
		return view('cpanel.providers.providers', compact('providers'));
	}

	public function create(){
		//get countries
		$countries = DB::table('country')->where('publish', 1)->get();
		//get categories
		$categories = DB::table('categories')->where('publish', 1)->get();
        //get types 

        $types = DB::table('memberships') -> get();

        $delivery_methods  = DB::table('delivery_methods') -> get();

		return view('cpanel.providers.create', compact('countries', 'categories','types','delivery_methods'));
	}


    protected function checkCountryCodeFormate($str){
         
               if(mb_substr(trim($str), 0, 1) === '+'){                         
                          return  $str;                     
                  }
                    
                  return '+'.$str;                  
    }
   
 
	public function store(Request $request){


       
	    $messages = [
            'full_name.required'       => 'لابد من ادخال الاسم  بالكامل ',
            'store_name.required'      => 'لابد من ادخال اسم المتجر ',
	        'phone.required'  => 'رقم الجوال مطلوب ولا ',
            'phone.unique'    => 'رقم الجوال مستخدم من قبل',
            'phone.regex'     => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
            'email.required'  => 'البريد الإلكترونى مطلوب ولا يمكن تركه فارغا',
            'email.unique'    => 'البريد الإلكترونى مستخدم من قبل',
            'email.email'     => 'خطأ فى صيغة البريد الإلكترونى',
            'country_code.required'               => 'لابد من ادحال كود الدوله ',
            'password_confirmation.required'      => 'لابد من تاكيد  كلمو المرور ',
            'password.required'                   => 'لابد من ادحال كلمة المرور ',
            'password.min'                        => 'لابد الا تقل كلمه المرور عن 8 احرف ',
            'password.confirmed'                  => 'لابد من تاكيد كلمه المرور ',
            'country_id.required'                 => 'لابد من  اختيار دوله ',
            'country_id.exists'                   => 'الدوله غير موجوده او ربما  تكون محذوفه ',
            'membership_id.required'              => 'لابد من تحديد نوع المتجر اولا ',
            'membership_id.exists'                => 'نوع المتجر غير موجود او ربما يكون قد حذف ',
            'city_id.required'                    => 'لايد من اختيار المدينة ',
            'city_id.exists'                      => 'المدينه المحتاره غير موجوده او ربما قد حذفت ',
            'commercial_photo.required'           => 'لابد من رفع صوره السجل التجاري ',
            'commercial_photo.mimes'              => 'لابد ان تكون الصوره من نوع  jpg or png ',
            'category_id.required'                => 'لأابد من اختيار القسم الرئيسي  للمتجر ',
            'category_id.exists'                  => 'القسم  الرئيسي غير موجود ',
            'longitude.required'                  => 'لابد من تحديد مكانك علي الحريطة ',
            'latitude.required'                   => 'لابد من تحديد مكانك علي الخريطه ',
            'delivery_method.required'            => 'لابد من اختيار وسيله توصيل واحده علي الافل ',
            'delivery_method.*.required'          => 'لابد من اختيار وسيله توصيل واحده علي الافل ',
            'delivery_method.array'               => 'لابد ان تكون وسائل التوصيل علي شكل مصفوفه ',
            'delivery_method.min'                 => 'لابد من اختيار وسيله توصيل واحده علي الاقل ',
            'delivery_method.*.exists'            => 'وسيله التوصيل غير موجوده ',
            'delivery_price.required'             => 'لابد من ادخال تكلفه التوصيل '
  
        ];


        $rules=[
            'full_name'              => 'required',
            'store_name'             => 'required',
            'phone'                  =>  array('required','unique:providers,phone','regex:/^(05|5)([0-9]{8})$/'),
            'country_code'           => 'required',
            'password_confirmation'  => 'required',
            'password'               => 'required|min:8|confirmed',         
            'country_id'             => 'required|exists:country,country_id',
            'membership_id'          => 'required|exists:memberships,membership_id',
            'city_id'                => 'required|exists:city,city_id',
            'commercial_photo'       => 'required|mimes:jpeg,png',
            'category_id'            => 'required|exists:categories,cat_id',
             'delivery_method'       => 'required|array|min:1',
             'delivery_method.*'     => 'required|exists:delivery_methods,method_id', // that means all of them must pass val
            'longitude'              => 'required',
            'latitude'               => 'required',            
            'delivery_price'         => 'sometimes'
        ];

 

//return response() -> json($request);

      $validator = Validator::make($request->all(),$rules, $messages);
   
                
    $validator->after(function ($validator) use($request) {

                if(empty($request -> delivery_method) or !$request -> delivery_method){
         
                      $validator->errors()->add('delivery_method', 'لابد من  اختيار وسائل التوصيل  او واحده ع الاقل ');
                 }

                if($request ->has('delivery_method')){
         
                      if(! is_array($request -> delivery_method)){
             
                          $validator->errors()->add('delivery_method', ' وسائل التوصيل لابد ان تطون مصفوفه .');

                      }
                }  

                 if(!empty($request -> delivery_method))
                  {
         
                        foreach ($request -> delivery_method as $method) {
                              
                              if(! is_numeric($method)){
                 
                                 $validator->errors()->add('delivery_method', ' خطا في  رقم وسيلة التوصيل .');
                              }else{

                                  if($method == 2 or $method == "2" )
                                    {  

                                        if(!$request -> delivery_price  or empty($request -> delivery_price) or $request -> delivery_price == NULL or $request -> delivery_price == ""  || ! is_numeric($request -> delivery_price))

                                        $validator->errors()->add('delivery_price', 'للابد من ادخال تكلفه التوصيل  وان تكون اعداد فقط ');
 
                                    }
                              }
                        }

                  }


     });
         


 
        if($validator->fails()){

               return redirect()->back()->with('errors', $validator->errors())->withInput();
        }

           $inputs = $request -> only('full_name','store_name','phone','country_id','city_id','membership_id','category_id','longitude','latitude');

           $inputs['country_code']  =  $this -> checkCountryCodeFormate($request->input('country_code'));
           $inputs['password']            =  md5($request -> password);
           $inputs['status']              = 1;
           $inputs['publish']             = 1;           
           $inputs['phoneactivated']      = '1';
           $inputs['delivery_price']      =  $request -> delivery_price ? $request -> delivery_price : 0 ;
           $inputs['token']               = $this -> getRandomString(128);



                    if($request -> hasFile('commercial_photo')){
                        $image  = $request -> commercial_photo ;
                        //save new image   
                        $image ->store('/','providers');
                        $nameOfImage = $image ->hashName();
                        $inputs['commercial_photo'] =  $nameOfImage;                           
                    }else{                        
                        $inputs['commercial_photo'] = "avatar_ic.png";
                    }
 


             $id = Providers::insertGetId($inputs);
 
			if($id){

                      if($request -> delivery_method_id && !empty($request->input('delivery_method_id'))){ 
                        foreach($request -> delivery_method_id as $deliveryId){
                            
                            DB::table('providers_delivery_methods') -> insert([                       
                                   'provider_id'       =>   $id,
                                   'delivery_method'   =>   $deliveryId                    
                                ]);                
                        }
                      }
				}
  
			$request->session()->flash('success', 'تم الاضافه بنجاح ');
			return redirect()->route('provider.show');		
	}

	public function edit($id){

         $data =[];
		//get Provider data 
		$data['provider']      = Providers::where('provider_id',$id) -> first();


        if(!$data['provider']){

             return abort('404');
        }

		   $city_id       = $data['provider']->city_id;
		    $country_id    = $data['provider']->country_id;
        $membership_id = $data['provider']->membership_id;
        $cat_id        = $data['provider']->category_id;

     
		//get countries
		 $data['countries']  = DB::table('country')-> where('publish',1) 
                                         -> select('country_id',
                                                  'country_ar_name',
                                                  'country_code',
                                                  DB::raw('IF(country_id = '.$country_id.', true, false) AS choosen')
                                                   )-> get();

        //get countries
          $data['memberships'] = DB::table('memberships')-> where('publish',1) -> select('membership_id','membership_ar_name',DB::raw('IF(membership_id = '.$membership_id.', true, false) AS choosen') )-> get();    
 
		//get country cities
		  $data['cities']       = DB::table('city')->select('city_id','city_ar_name','country_id',DB::raw('IF(city_id = '.$city_id.', true, false) AS choosen'))->get();

		//get selected cats 
		 $data['categories']    = DB::table('categories')->where('publish',1) 
                                             ->select( 'cat_ar_name',
                                                       'cat_id',
                                                       DB::raw("IF(cat_id = {$cat_id}, true, false) AS choosen")
                                              )->get();
 
         $data['delivery_methods']=  DB::table("delivery_methods")->select('method_id','method_ar_name',
                                                                DB::raw('IF((SELECT count(providers_delivery_methods.id) FROM providers_delivery_methods WHERE providers_delivery_methods.delivery_method = delivery_methods.method_id AND providers_delivery_methods.provider_id = '.$id.') > 0, 1, 0) AS choosen'))
                                                       ->get();

		return view('cpanel.providers.edit', $data);


	}

	public function update(Request $request){
        

        $messages = [
            'full_name.required'       => 'لابد من ادخال الاسم  بالكامل ',
            'store_name.required'      => 'لابد من ادخال اسم المتجر ',
            'phone.required'  => 'رقم الجوال مطلوب ولا ',
            'phone.unique'    => 'رقم الجوال مستخدم من قبل',
            'phone.regex'     => 'صيغه الهاتف غير صحيحه لابد ان تبدا ب 5 او 05',
            'email.required'  => 'البريد الإلكترونى مطلوب ولا يمكن تركه فارغا',
            'email.unique'    => 'البريد الإلكترونى مستخدم من قبل',
            'email.email'     => 'خطأ فى صيغة البريد الإلكترونى',
            'country_code.required'               => 'لابد من ادحال كود الدوله ',
            'password_confirmation.required'      => 'لابد من تاكيد  كلمة  المرور ',
            'password.required'                   => 'لابد من ادحال كلمة المرور ',
            'password.min'                        => 'لابد الا تقل كلمه المرور عن 8 احرف ',
            'password.confirmed'                  => 'لابد من تاكيد كلمه المرور ',
            'country_id.required'                 => 'لابد من  اختيار دوله ',
            'country_id.exists'                   => 'الدوله غير موجوده او ربما  تكون محذوفه ',
            'membership_id.required'              => 'لابد من تحديد نوع المتجر اولا ',
            'membership_id.exists'                => 'نوع المتجر غير موجود او ربما يكون قد حذف ',
            'city_id.required'                    => 'لايد من اختيار المدينة ',
            'city_id.exists'                      => 'المدينه المحتاره غير موجوده او ربما قد حذفت ',
            'commercial_photo.required'           => 'لابد من رفع صوره السجل التجاري ',
            'commercial_photo.mimes'              => 'لابد ان تكون الصوره من نوع  jpg or png ',
            'category_id.required'                => 'لأابد من اختيار القسم الرئيسي  للمتجر ',
            'category_id.exists'                  => 'القسم  الرئيسي غير موجود ',
            'longitude.required'                  => 'لابد من تحديد مكانك علي الحريطة ',
            'latitude.required'                   => 'لابد من تحديد مكانك علي الخريطه ',
            'delivery_method.required'            => 'لابد من اختيار وسيله توصيل واحده علي الافل ',
            'delivery_method.*.required'          => 'لابد من اختيار وسيله توصيل واحده علي الافل ',
            'delivery_method.array'               => 'لابد ان تكون وسائل التوصيل علي شكل مصفوفه ',
            'delivery_method.min'                 => 'لابد من اختيار وسيله توصيل واحده علي الاقل ',
            'delivery_method.*.exists'            => 'وسيله التوصيل غير موجوده ',
            'delivery_price.required'             => 'لابد من ادخال تكلفه التوصيل '
  
        ];


        $rules=[
            'full_name'              => 'required',
            'store_name'             => 'required',
            'phone'                  =>  array('required','unique:providers,phone,'.$request->input('provider_id').',provider_id','regex:/^(05|5)([0-9]{8})$/'),
            'country_code'           => 'required',
            'country_id'             => 'required|exists:country,country_id',
            'membership_id'          => 'required|exists:memberships,membership_id',
            'city_id'                => 'required|exists:city,city_id',
            'category_id'            => 'required|exists:categories,cat_id',
             'delivery_method'       => 'required|array|min:1',
             'delivery_method.*'     => 'required|exists:delivery_methods,method_id', // that means all of them must pass val
            'longitude'              => 'required',
            'latitude'               => 'required',            
            'delivery_price'         => 'sometimes'
        ];


 
   if($request -> hasFile('commercial_photo')){

      $rules['commercial_photo'] = 'required|mimes:jpeg,png';

    
    }

     if($request ->has('password')){
 
          $rules['password_confirmation'] = 'required';
          $rules['password']              = 'required|min:8|confirmed';

      }

      $validator = Validator::make($request->all(),$rules, $messages);
   
                
    $validator->after(function ($validator) use($request) {

                if(empty($request -> delivery_method) or !$request -> delivery_method){
         
                      $validator->errors()->add('delivery_method', 'لابد من  اختيار وسائل التوصيل  او واحده ع الاقل ');
                 }

                if($request ->has('delivery_method')){
         
                      if(! is_array($request -> delivery_method)){
             
                          $validator->errors()->add('delivery_method', ' وسائل التوصيل لابد ان تطون مصفوفه .');

                      }
                }  

                 if(!empty($request -> delivery_method))
                  {
         
                        foreach ($request -> delivery_method as $method) {
                              
                              if(! is_numeric($method)){
                 
                                 $validator->errors()->add('delivery_method', ' خطا في  رقم وسيلة التوصيل .');
                              }else{

                                  if($method == 2 or $method == "2" )
                                    {  

                                        if(!$request -> delivery_price  or empty($request -> delivery_price) or $request -> delivery_price == NULL or $request -> delivery_price == ""  || ! is_numeric($request -> delivery_price))

                                        $validator->errors()->add('delivery_price', 'للابد من ادخال تكلفه التوصيل  وان تكون اعداد فقط ');
 
                                    }
                              }
                        }

                  }




     });
         


 
        if($validator->fails()){

               return redirect()->back()->with('errors', $validator->errors())->withInput();
        }


           $inputs = $request -> only('full_name','store_name','phone','country_id','city_id','membership_id','category_id','longitude','latitude');


                 if($request -> hasFile('commercial_photo')){

                      //delete the previous image from storage 
                       if(Storage::disk('providers')->exists($request -> input('commercial_photo')))
                       {
                             
                            Storage::disk('providers')->delete($request -> input('commercial_photo'));

                       }
 
                         $image  = $request -> commercial_photo ;
                        //save new image   
                        $image ->store('/','providers');
                        $nameOfImage = $image ->hashName();
                        $inputs['commercial_photo'] =  $nameOfImage;                           
                 }


                 if($request -> has('password')){
                   
                     $inputs['password']            =  md5($request -> password);
                 }
                        

           $inputs['country_code']  =  $this -> checkCountryCodeFormate($request->input('country_code'));
           $inputs['status']              = 1;
           $inputs['publish']             = 1;           
           $inputs['phoneactivated']      = '1';
           $inputs['delivery_price']      =  $request -> delivery_price ? $request -> delivery_price : 0 ;
 
             Providers::where('provider_id',$request -> provider_id) -> update($inputs);
  

                      if($request -> delivery_method && !empty($request->input('delivery_method'))){ 

                        DB::table('providers_delivery_methods') -> where('provider_id',$request -> provider_id) -> delete();

                        foreach($request -> delivery_method  as $deliveryId){
                            
                            DB::table('providers_delivery_methods') -> insert([                       
                                   'provider_id'       =>   $request -> provider_id,
                                   'delivery_method'   =>   $deliveryId                    
                                ]);                
                        }
                      }
  
			$request->session()->flash('success', 'Provider updated successfully');
			return redirect()->route('provider.show');
		 
	}

	public function getProducts($provider_id){


    //products with last inserted image
 
           $products = DB::table('providers') 
                            -> join('products','providers.provider_id','=','products.provider_id')
                            ->where('providers.provider_id',$provider_id)
                             ->select(

                                    'products.id AS product_id',
                                     'products.category_id',
                                     'products.description',
                                     'products.title',
                                     'providers.store_name',
                                     'providers.provider_id',
                                     'products.price',
                                     'products.publish' ,
                                     DB::raw("CONCAT('". url('/') ."','/\providerProfileImages/',providers.profile_pic) AS profile_pic")

                                    
                                )
                            -> get();
                        
                        
                            
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


  	 
		return view('cpanel.providers.products',compact('products'));
	}


    public function changeProductStatus(Request $request){

        $product = DB::table('products') -> whereId($request -> product_id)  -> select('publish') ->  first();

       if(!$product){

        return abort('404');
       }

          //reverse status 
       $status  = $request -> currentstatus  == 0  ? 1 : 0;


      DB::table('products') -> whereId($request -> product_id)  -> update(['publish'=>$status]);

      return response() -> json(['status' => $status]);

    }

	 
/*
	public function getProviderIncomeView(){
		$providers = Providers::get();
		return view('cpanel.providers.income', compact('providers'));
	}

	public function incomeSearch(Request $request){
		$type = $request->input('type');
		if($type == 'provider'){
            $table = 'providers';
            $col   = 'providers.provider_id';
            $cond  = 'orders_headers.provider_id';
            $money = '(orders_headers.net_value - marketer_value)AS credit';
            $app   = 'orders_headers.app_value AS app';
            $marketer = 'orders_headers.marketer_value AS marketer';
        }elseif($type == 'delivery'){
            $table = 'deliveries';
            $col   = 'deliveries.delivery_id';
            $cond  = 'orders_headers.delivery_id';
            $money = '(orders_headers.delivery_price - orders_headers.delivery_app_value) AS credit';
            $app   = 'orders_headers.delivery_app_value AS app';
            $marketer = 'orders_headers.marketer_delivery_value AS marketer';
        }else{
            return response()->json(['status' => false, 'errNum' => 3, 'msg' => $msg[3]]);
        }


        $conditions[] = [$cond, '=', $request->input('id')];
        $conditions[] = ['orders_headers.status_id' , '=', 4];
        if(!empty($request->input('from')) && !empty($request->input('to'))){
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '>=', $request->input('from')];
        	$conditions[] = [DB::raw('DATE(orders_headers.created_at)'), '<=', $request->input('to')];
        }

        // var_dump($conditions);
        // die();
	    $result = DB::table('orders_headers')->where($conditions)
	        						         ->join($table, $cond, '=', $col)
	        						         ->join('order_details', 'orders_headers.order_id', '=', 'order_details.order_id')
	        						         ->select(
			        						   		'orders_headers.order_code', 
			        						   		'orders_headers.total_qty', 
			        						   		'orders_headers.total_value', 
			        						   		'orders_headers.order_id AS invo_no',
			        						   		DB::raw($money), 
			        						   		DB::raw($app), 
			        						   		DB::raw($marketer), 
			        						   		DB::raw('COUNT(order_details.meal_id) AS mealsCount'), 
			        						   		'orders_headers.order_id',
			        						   		'orders_headers.balance_status'
			        						  )
	        						         ->groupBy('orders_headers.order_id')
	        						         ->get();
	    $total = 0;
	    $data  = '';
	   	if(!empty($result)){
	   		foreach($result AS $row){
	   			$total += $row->credit;
	   			$data .= '<tr>
                                <td>'.$row->invo_no.'</td>
                                <td>'.$row->order_code.'</td>
                                <td>'.ROUND($row->total_value,2).'</td>
                                <td>'.ROUND($row->credit,2).'</td>
                                <td>'.ROUND($row->app,2).'</td>
                                <td>'.ROUND($row->marketer,2).'</td>
                                <td>'.(($row->balance_status == 1)? 'Pending' : 'Done').'</td>
                            </tr>';
	   		}
	   	}

	   	return response()->json(['data'=>$data, 'total'=>round($total,2)]);
	}*/
}
