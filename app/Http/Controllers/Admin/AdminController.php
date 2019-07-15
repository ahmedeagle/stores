<?php

namespace App\Http\Controllers\Admin;

/**
 * 
 * @author Ahmed Emam   <ahmedaboemam123@gmail.com>
 */
use Log;
use App\Http\Controllers\Controller;
use App\Admin;
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
use Hash;
use Illuminate\Foundation\Validation\ValidatesRequests;

//Class needed for login and Logout logic
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Auth facade
use Auth;

class AdminController extends Controller
{

	//Trait
    use AuthenticatesUsers;

	protected $redirectTo = 'admin/home';

	//Custom guard for seller
    protected function guard()
    {
      return Auth::guard('admin');
    }

	public function loginView(){
		return view('cpanel.admin.login');
	}

	public function getLogin(){
		return redirect()->route('loginView');
	}



    public function postLogin(Request $r){
        // 1- Validator::make()
        // 2- check if fails
        // 3- fails redirect or success not redirect

        $this->validate($r,[
            'email'    => 'required|email',
            'password' => 'required',
         ],[
            "email.required"     => 'لايد من ادخال البريد الالكتروني ',
            "password.required"  =>'لابد من ادخال كلمة المرور ',
            "email.email"        => 'صيغة بريد الكتروني غير صالحه ',
        ]);
             
            $email     = $r->input('email');
            $password  = $r->input('password');
            
            $admin= Admin::Where('email' ,$email)->first();

            if($admin && $admin -> publish == 0){

            	 return redirect()->back()->with('error' , 'لايمكن الدخول الي حساب الان يمكنك الرجوع الي اداره الموقع ');
            }

            if( $admin && Hash::check($password, $admin->password) ){
                // login the Driver
                Auth::guard('admin')->login($admin ,$r->has('remember'));
                return redirect()->intended('/admin/home');
            }
            // failed
            return redirect()->back()->with('error' , 'فشل في تسجيل الدخول الرجاء المحاوله مجددا ');
       
    }



	public function index(){
		$admins = Admin::all();
		return view('cpanel.admin.admins', compact('admins'));
	}
	

	public function edit($id){

		$admin       = Admin::findOrFail($id);
 		return view('cpanel.admin.edit', compact('admin'));

 	}


 	public function delete($id){

       $admin       = Admin::findOrFail($id);
       $admin       -> delete();

 		return redirect(Route('admins.show')) -> with('msg','Admin Deleted successfully') ;
 	}


	public function update(Request $request){
       
       $rules=[
				'full_name' => 'required|max:100',
				'email'     => 'required|unique:admin,email,'.$request->input('email').',email',
				'publish'  => 'required|in:0,1'

		     ];


		     if($request-> has('password')){

		     	$rules['password']               = 'required|min:6|confirmed';
		     	$rules['password_confirmation']  = 'required';
 
		     }

      $validate = Validator::make($request->all(), $rules);

		if($validate->fails()){
			$request->session()->flash('errors', $validate->errors()->first());
			return redirect()->back()->withInput();
		}


 
		if($request->input('password')){

 
			$update = Admin::where('id', $request->input('id'))
						  ->update([
						  		'full_name' => $request->input('full_name'),
						  		'email'     => $request->input('email'),
						  		'password'     => bcrypt($request->input('password')),
						  		'publish'       => $request -> publish,
 						  	]);
		}else{
			$update = Admin::where('id', $request->input('id'))
						  ->update([
						  		'full_name' => $request->input('full_name'),
						  		'email'     => $request->input('email'),
 						  		'publish'   => $request -> publish,
 						  	]);
		}


		if($update){
			$request->session()->flash('success', 'User has been updated successfully');
			return redirect()->route('admins.show');
		}else{
			$request->session()->flash('errors', 'Failed to update Please try again later');
			return redirect()->back()->withInput();
		}
	 


	}


	public function create(Request $request){

		$validator = Validator::make($request->all(), [
			'name'     => 'required', 
			'email'    => 'email|unique:admin',
			'password' => 'min:8',
			'publish'  => 'required|in:0,1'
		]);

		if($validator->fails()){
		//	return redirect()->back()->with('errors', $validator->errors())->withInput();

			return response()->json($validator->errors() -> first());
		} 

 			$insert = DB::table('admin')->insert([
							'full_name' => $request->input('name'),
							'email'     => $request->input('email'),
							'password'  => bcrypt($request->input('password')),
							'publish'   => $request-> publish, 
					  ]);

			if($insert){
				return redirect()->back()->with('msg', 'Admin added successfully');
			}else{
				$err = array('Failed to add admin');
				//return redirect()->back()->with('errors', $err)->withInput();
				return response()->json($err);
			}
		 
	}
 
	public function create_admin(){
		return view('cpanel.admin.create');
	}

	public function getCountryCitites(Request $request){
		$country = $request->input('country_id');

		$cities  = DB::table('city')->where('publish', 1)->get();

	}


	  public function logout(Request $request)
    {
             Auth::guard('admin')->logout();
	        $request->session()->flush();
	        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
