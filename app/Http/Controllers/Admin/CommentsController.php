<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Ahmed Emam <ahmedaboemam@gmail.com>
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

class CommentsController extends Controller
{
	public function __construct(){
		
	}

	public function getComments(){
		$users    = User::select('user_id', 'full_name')->get();
		$comments = DB::table('product_comments')
					->join('users', 'product_comments.user_id', '=', 'users.user_id')
					->join('products', 'product_comments.product_id', '=', 'products.id')
					->select(
							'product_comments.id', 
							'product_comments.comment', 
							DB::raw('DATE(product_comments.created_at) AS created'), 
							'users.full_name', 
							DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
							'products.title', 
							 'products.id as product_id',
							 'is_read',
 							DB::raw('DATE(product_comments.created_at) AS created')
 						)    
					->orderBy('product_comments.id', 'DESC')
					->paginate(40);

			if(isset($comments) && $comments -> count() > 0 ){

				  foreach ($comments as $key => $comment) {

				  	 $image = DB::table('product_images') -> where('product_id',$comment -> product_id) -> select('image') ->  first();
				  	 if($image){
                         
                        $comment -> product_image =  env('APP_URL').'/public/products/'.$image -> image  ;
				  	 }else{

                             $comment -> product_image = "";
				  	 }
				  }

				

			}


		return view('cpanel.comments.comments', compact('users', 'comments'));
	}


	public function search($from, $to, $user, $phone){
		$conditions = [];
		if(!in_array($from, ["null", null, ""]) && !in_array($to, ["null", null, ""])){
			$conditions[] = [DB::raw('DATE(product_comments.created_at)'), '<=', $to];
			$conditions[] = [DB::raw('DATE(product_comments.created_at)'), '>=', $from];
		}

		if(!in_array($user, ["null", null, ""])){
			$conditions[] = ['product_comments.user_id', '=', $user];
		}

		if(!in_array($phone, ["null", null, ""])){
			$conditions[] = ['product_comments.user_id', 'LIKE', "%".$phone];
		}

		$users = User::select('user_id', 'full_name')->get();
		if(!empty($conditions)){
			$comments = DB::table('product_comments')
						->where($conditions)
						->join('users', 'product_comments.user_id', '=', 'users.user_id')
						->join('products', 'product_comments.product_id', '=', 'products.id')
						->select(
								'product_comments.id', 
								'product_comments.comment', 
								DB::raw('DATE(product_comments.created_at) AS created'), 
								'users.full_name', 
								DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
								'products.title', 
								DB::raw('DATE(product_comments.created_at) AS created')
								)
						->orderBy('product_comments.id', 'DESC')
						->paginate(40);
		}else{
			$comments = DB::table('product_comments')
						->join('users', 'product_comments.user_id', '=', 'users.user_id')
						->join('products', 'product_comments.product_id', '=', 'products.id')
						->select(
								'product_comments.id', 
								'product_comments.comment', 
								DB::raw('DATE(product_comments.created_at) AS created'), 
								'users.full_name', 
								DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
								'products.title', 
 								DB::raw('DATE(product_comments.created_at) AS created')
								)
						->orderBy('product_comments.id', 'DESC')
						->paginate(40);
		}

		return view('cpanel.comments.comments', compact('users', 'comments'));
	}

	public function delete($id, Request $request){
		$check = DB::table('product_comments')->where('id', $id)->delete();
		if($check){
			$request->session()->flash('success', 'Deleted successfully');
		}else{
			$request->session()->flash('err', 'Failed to delete please try again later');
		}

		return redirect()->back();
	}

	public function today(){
		$comments = DB::table('product_comments')
					->where(DB::raw('DATE(product_comments.created_at)'), date('Y-m-d', time()))
					->join('users', 'product_comments.user_id', '=', 'users.user_id')
					->join('products', 'product_comments.product_id', '=', 'products.id')
					->select(
							'product_comments.id', 
							'product_comments.comment', 
							'products.id as product_id', 
							DB::raw('DATE(product_comments.created_at) AS created'), 
							'users.full_name', 
							DB::raw('CONCAT(users.country_code, users.phone) AS phone'), 
							'products.title', 
							'is_read',
 							DB::raw('DATE(product_comments.created_at) AS created')
							)
					->orderBy('product_comments.id', 'DESC')
					->paginate(40);

 
			if(isset($comments) && $comments -> count() > 0 ){

				  foreach ($comments as $key => $comment) {

				  	 $image = DB::table('product_images') -> where('product_id',$comment -> product_id) -> select('image') ->  first();
				  	 if($image){
                         
                        $comment -> product_image =  env('APP_URL').'/public/products/'.$image -> image  ;
				  	 }else{

                             $comment -> product_image = "";
				  	 }
				  }

				

			}

		return view('cpanel.comments.today', compact('comments'));
	}


	public function comment_seen($comment_id){

        
        $comment = DB::table('product_comments') -> whereId($comment_id) -> first();

         if(!$comment){

         	return abort('404');
         }

        
            DB::table('product_comments') -> whereId($comment_id) -> update(['is_read' => 1]);

            return redirect() -> back() -> with('success','تمت العمليه بنجاح ');

	}
}
