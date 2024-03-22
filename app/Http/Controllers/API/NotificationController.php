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
        $resp = $client->request('POST', "https://fcm.googleapis.com/v1/projects/gi1-info-app/messages:send", [
            'headers' => [
                'Authorization' =>'Bearer c9afc9a63f4b178598d17edfa08d92a9f1e723b6',
                'Content-Type' => 'application/json'
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
