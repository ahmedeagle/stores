<?php

namespace App\Http\Controllers;

/**
 * @author Ahmed Emam <ahmedaboemam123@gmail.com>
 */
class PushNotificationController extends Controller
{
	// set variables

	//define firebase_keys
	protected $api_key;

	public $user_key = "AAAAd-DFCIE:APA91bGBWlUTlTFUG1lnB6hJZx0RxvO_QawRoaiZtFd3wwOtHgqVOejg8Vrp6hgZYs2mweEctvqa-vN8ft_AIhzWYgOB3Mcck-mfQYZMPWe8CPIPGHdFqSufWtX6IJkYRdgSV2W3YtJx";
	public $provider_key = "AAAA6DK1Ckc:APA91bEivhMlo-HyBut78BF3tBcgt1o0YbcZGzbDPXgan97pxOjPiC78kVcbHzQUf7NX5DMKkeRzT1cn5_YuQHlr3GBWvBvsSAhpQ90hKnZ-oCkIRYDenzt6K8Wq9mhUvSkJSCUaxA8E";
	public $delivery_key = "AAAAxFm5gA4:APA91bFUrkP38VckrgQiBb-zvgSfkrflB2LZnxhpPiH-i5QQrv93xwz9QMYjoNm6Si_9gngbWUQbzPE0NspTPF3sao7__5ZR-xrfd881jW5H_H01oWPp8mHFdoUXr0xKSncoYDHhCgXo";

	protected $push_notif_icon = '';

	public function send($device_token, $data, $key)
	{
		//device id
		$this->api_key = $key;

		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			'to' => $device_token,
			'notification' => $data,
		);
		$fields = json_encode($fields);
		$headers = array(
			'Authorization: key=' . $this->api_key,
			'Content-Type: application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;

	}

	public function sendNotificationToWebBrowser($subscribeToken, $data)
	{

		//FCM key

		$server_key = "AAAA6rW-n98:APA91bFE83nx5zmyzBC1y-3l7tj5EUDZe1j8PQ2eMnPr_rmcx0GLDiKwHQ7aPNs8kD64Ql37962h2JfKazeUTyns2OalDx6T7pea6KZbWb_60V_Gk1EIRob2tm89Occgaz_jZyN62ALy";

		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			'to' => $subscribeToken,
			'notification' => $data,
		);
		$fields = json_encode($fields);
		$headers = array(
			'Authorization: key=' . $server_key,
			'Content-Type: application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;

	}
}
