<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function sendNotification(){
        $client = new Client();
        $resp = $client->request('POST', "https://fcm.googleapis.com/v1/projects/myproject-gi1-info-app/messages:send", [
            'headers' => [
                'Authorization' =>'Bearer 27595911584-evlpcfqpe2npa9dt8v1kmkd1shafg6fq.apps.googleusercontent.com'
            ],
            'form_params' => [
                "message" => [
                    "topic" => "all",
                    "notification" => [
                        "title" => "Testing Message",
                        "body" => "This is a Test notification from the server."
                    ],
                    "data" => [
                        "uid" => "test"
                    ],
                    "android" => [
                        "notification" => [
                            "body" => "This is a Test notification from the server"
                        ]
                    ],
                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "category" => "Gi1 Prime App"
                            ]
                        ]
                    ]
                    
                ]
            ]
        ]);

        return response()->json($resp);

    }
}
