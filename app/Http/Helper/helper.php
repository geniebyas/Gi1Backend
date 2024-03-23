<?php

use App\Models\PersonalNotification;
use App\Models\PublicNotification;
use App\Models\User;
use App\Models\UsersSetting;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

if (!function_exists('p')) {
    function p($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}


function generateReferCode()
{
    do {
        $referCode = strtolower(Str::random(6));
    } while (UsersSetting::where('refer_code', $referCode)->exists());

    return $referCode;
}

if (!function_exists('addCoins')) {
    function addCoins($uid, $action_id)
    {
        $client = new Client();
        if ($uid != null && $action_id != null) {

            $resp = $client->request('POST', "https://api.gi1superapp.com/api/coins/add", [
                'headers' => [
                    'uid' => $uid
                ],
                'form_params' => [
                    'action_id' => $action_id,
                    'type' => "add"
                ]
            ]);
            return $resp;
        }
    }
}

if(!function_exists('sendPublicNotification')){
    function sendPublicNotification(PublicNotification $data){
        define("GOOGLE_APPLICATION_CREDENTIALS", __DIR__ . '/../Controllers/API/gi1-info-app-c9afc9a63f4b.json');
        $factory = (new Factory)->withServiceAccount(GOOGLE_APPLICATION_CREDENTIALS);
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('topic', $data->topic)
            ->withNotification(['title' => $data->title, 'body' => $data->body])
            ->withData([
                'img_url' => $data->img_url,
                'topic' => $data->topic,
                'android_route' => $data->android_route
            ]);

        $messaging->send($message);

        PublicNotification::create([
            "title" => $data->title,
            "body" => $data->body,
            "img_url" => $data->img_url,
            "android_route" => $data->android_route,
            "topic" => $data->topic
        ]);

        return response()->json(
            [
                'message' => "Notification Published",
                'status' => 1,
                'data' => $data
            ]
            );
    }
}

if(!function_exists('sendPersonalNotification')){
    function sendPersonalNotification(PersonalNotification $data){
        define("GOOGLE_APPLICATION_CREDENTIALS", __DIR__ . '/../Controllers/API/gi1-info-app-c9afc9a63f4b.json');
        $factory = (new Factory)->withServiceAccount(GOOGLE_APPLICATION_CREDENTIALS);
        $messaging = $factory->createMessaging();

        $user = User::where("uid",$data->reciever_uid)->get()->first();

        if(!is_null($user->token)){
        $message = CloudMessage::withTarget('token', $user->token)
            ->withNotification(['title' => $data->title, 'body' => $data->body])
            ->withData([
                'sender_uid'=>$data->sender_uid,
                'reciever_uid'=>$data->reciever_uid,
                'img_url' => $data->img_url,
                'android_route' => $data->android_route
            ]);

        $messaging->send($message);

        PersonalNotification::create([
            "title" => $data->title,
            "body" => $data->body,
            "img_url" => $data->img_url,
            "android_route" => $data->android_route,
            "sender_uid"=>$data->sender_uid,
            "reciever_uid"=>$data->reciever_uid
        ]);

        return response()->json(
            [
                'message' => "Notification Published",
                'status' => 1,
                'data' => $data
            ]
            );
        }
    }
}




if (!function_exists('removeCoins')) {
    function removeCoins($uid, $action_id)
    {
        $client = new Client();
        if ($uid != null && $action_id != null) {
            $resp = $client->request('POST', "https://api.gi1superapp.com/api/coins/add", [
                'headers' => [
                    'uid' => $uid
                ],
                'form_params' => [
                    'action_id' => $action_id,
                    'type' => "remove"
                ]
            ]);

            return $resp;
        }
    }
}
