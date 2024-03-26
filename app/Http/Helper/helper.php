<?php

use App\Models\CoinsActions;
use App\Models\PersonalNotification;
use App\Models\PublicNotification;
use App\Models\User;
use App\Models\UsersSetting;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;




define("GOOGLE_APPLICATION_CREDENTIALS", __DIR__ . '/../Controllers/API/gi1-info-app-c9afc9a63f4b.json');


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
    function addCoins($uid, $action_id, $description)
    {
        $client = new Client();
        if ($uid != null && $action_id != null) {

            $resp = $client->request('POST', "https://api.gi1superapp.com/api/coins/add", [
                'headers' => [
                    'uid' => $uid
                ],
                'form_params' => [
                    'action_id' => $action_id,
                    'type' => "add",
                    'description' => $description
                ]
            ]);
            $action = CoinsActions::find($action_id);
            try {
                sendPersonalNotification(new PersonalNotification([
                    "title" => "Congratulations! You've won $action->amount coins 🎉",
                    "body" => "You've been awarded $action->amount coins for your $action->name task. Keep up the good work and enjoy your rewards",
                    "receiver_uid" => $uid
                ]));
            } catch (Throwable $e) {
            }

            return $resp;
        }
    }
}

if (!function_exists('sendPublicNotification')) {
    function sendPublicNotification(PublicNotification $data)
    {
        $factory = (new Factory)->withServiceAccount(GOOGLE_APPLICATION_CREDENTIALS);
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('topic', $data->topic)
            ->withNotification(['title' => $data->title, 'body' => $data->body])
            ->withData([
                'img_url' => $data->img_url,
                'topic' => $data->topic,
                'android_route' => $data->android_route
            ]);

        try {

            $messaging->send($message);
        } catch (Throwable $e) {
        }

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

if (!function_exists('sendPersonalNotification')) {
    function sendPersonalNotification(PersonalNotification $data)
    {

        $factory = (new Factory)->withServiceAccount(GOOGLE_APPLICATION_CREDENTIALS);
        $messaging = $factory->createMessaging();

        $user = User::where("uid", $data->receiver_uid)->get()->first();

        if (!is_null($user->token)) {
            $message = CloudMessage::withTarget('token', $user->token)
                ->withNotification(['title' => $data->title, 'body' => $data->body])
                ->withData([
                    'sender_uid' => $data->sender_uid,
                    'receiver_uid' => $data->receiver_uid,
                    'img_url' => $data->img_url,
                    'android_route' => $data->android_route
                ]);
            try {

                $messaging->send($message);
            } catch (Throwable $e) {
            }
            PersonalNotification::create([
                "title" => $data->title,
                "body" => $data->body,
                "img_url" => $data->img_url,
                "android_route" => $data->android_route,
                "sender_uid" => $data->sender_uid,
                "receiver_uid" => $data->receiver_uid
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
