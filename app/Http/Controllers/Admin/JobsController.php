<?php

namespace App\Http\Controllers\Admin;

/**
 *
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */

use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController as Push;
use App\Http\Controllers\NotificationController as NotifyC;
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
use Carbon\Carbon;
use DateTime;

class JobsController extends Controller
{

	public function __construct()
	{

	}


	public function show()
	{


		$jobs = DB::table('providers')
			->join('provider_jobs', 'providers.provider_id', 'provider_jobs.provider_id')
			// -> where('provider_jobs.publish',1)
			->select('provider_jobs.id AS job_id',
				'job_title',
				'job_desc',
				'store_name',
				'provider_jobs.publish',
				'provider_jobs.provider_id',
				'provider_jobs.created_at',
				DB::raw("CONCAT('" . env('APP_URL') . "','/public/providerProfileImages/',providers.profile_pic) AS store_image"),
				DB::raw("(SELECT(COUNT(applicants.id) )FROM applicants WHERE applicants.job_id = provider_jobs.id) AS applicants")

			)
			->get();


		return view('cpanel.jobs.show', compact('jobs'));

	}

	public function jobsPublishing(Request $request)
	{


		$job_id = $request->job_id;

		$status = $request->status;  // 0 unpublish 1 publish

		$job = DB::table('provider_jobs')->whereId($job_id)->first();

		if (!$job) {
			return response()->json(['error' => ' الوظيفه  غير موجود او ربما تمت حذفه ']);
		}

		$provider = DB::table('providers')->where('provider_id', $request->provider_id)->select('device_reg_id', 'provider_id')->first();


		if (!$provider) {

			return response()->json(['error' => 'صاحب العرض غير موجود او ربما يكون  محذوف  ']);
		}


		$notif_data = array();
		if ($status == 1) { // publish

			$updated = DB::table('provider_jobs')->whereId($job_id)->update(['publish' => '1']);

			$notif_data['title'] = 'نشر  وظيفه  ';
			$notif_data['message'] = "تم نشر  الوظيفه  الخاص بكم  {$job -> job_title}";
			$notif_data['job_id'] = $job_id;
			$notif_data['notif_type'] = 'jobs';


		} elseif ($status == 0) {  // unpublish

			$updated = DB::table('provider_jobs')->whereId($job_id)->update(['publish' => '0']);

			$notif_data['title'] = 'تم ايقاف  نشر  الوظيفه  الخاص بكم ';
			$notif_data['message'] = "تم ايقاف نشر   الوظيفه  الخاص بكم  {$job -> job_title}";
			$notif_data['request_id'] = $job_id;
			$notif_data['notif_type'] = 'jobs';

		} else {

			return response()->json(['error' => 'حاله  الوظيفه  غير صحيحه من فضلك حاول مجداا ']);
		}

		if ($updated) {


			//send notification to mobile Firebase to provider
			if ($provider->device_reg_id && $provider->device_reg_id != NULL) {

				$push_notif = (new Push())->send($provider->device_reg_id, $notif_data, (new Push())->provider_key);
			}


			DB::table("notifications")
				->insert([
					"en_title" => $notif_data['title'],
					"ar_title" => $notif_data['title'],
					"en_content" => $notif_data['message'],
					"ar_content" => $notif_data['message'],
					"notification_type" => 7,
					"actor_id" => $provider->provider_id,
					"actor_type" => "provider",
					"action_id" => $job_id
				]);


			return response()->json(['success' => 'تم تغيير حاله  الوظيفه  بنجاح وتم اشعار التاجر بها ', 'status' => $status]);

		} else {
			return response()->json(['error' => 'فشل في  تغيير حاله  الوظيفه  من فضلك حاول  مجددا ']);
		}

	}


}


