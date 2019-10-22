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
    public $user_key = "AAAA7oOKXvM:APA91bG-HKMnpy7QwuUKaPlA5cnC1vL8SS4yIdmfuuXJf_XaY0VqaNZIBaAeKSLINCG4pCFp9ntKfYgCl1dCdo2WhbWYtJxmFTv0rx0OL0-N9Wlx54fCKClLKh5QpmLcsySgqpk2Silu";
    public $provider_key = "AAAAZa06vKA:APA91bHaC4Aj-I3DeT-Lc0cczqS1kMYZkSxKZ_JEYHp3o9O4id0lTKEsDZlmcZeRZF0qROs9HBB77tZ5twjokBFLL0qTowVek4Ws3LUlWojWBHN4x4Zjg1yiwA64s-q3yYepcnn-YQtN";
    public $delivery_key = "AAAACWiFdaE:APA91bFIKnRGXO4AQCRCg4FQxkFtC4tsPDfYjbzZA8O2PKR9huiVWWFdPj5lIS0D-wyHSbT95MgVv1mhUwd_LOnh7o3xOCRVoMLgitnWNOqxG0XsJ8E7d76g63xFLpSEi_-oTPbwQmZ_";
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
