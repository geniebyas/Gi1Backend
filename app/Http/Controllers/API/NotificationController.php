<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalNotification;
use App\Models\PublicNotification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class NotificationController extends Controller
{
    //
    public function sendNotification()
    {
        // sendPublicNotification(new PublicNotification([
        //     "title" => "Test Notification",
        //     "body" => "Test Notification Body",
        //     "img_url" => "https://www.gi1superapp.com/images/websitelogo.png",
        //     "topic" => "all"
        // ]));

        sendPersonalNotification(new PersonalNotification([
            "title" => "Personal Notification Test",
            "body" => "Personal Notification Test Body",
            "sender_uid" =>"BkjsdvfaCVQgxFDTtEjQr5ERpTu2",
            "reciever_uid"=>"irQkpwCcDlPExfZd6LzhspuJEF63"
        ]));

        // define("GOOGLE_APPLICATION_CREDENTIALS", __DIR__ . '/gi1-info-app-c9afc9a63f4b.json');
        // $factory = (new Factory)->withServiceAccount(GOOGLE_APPLICATION_CREDENTIALS);
        // $messaging = $factory->createMessaging();


        $messagee = new RawMessageFromArray([
            'notification' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#notification
                'title' => 'Default title',
                'body' => 'Default body',
            ],
            'data' => [
                'key' => 'Value',
            ],
            'android' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
                'notification' => [
                    'title' => 'Android Title',
                    'body' => 'Android Body',
                ],
            ],
            'apns' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => 'iOS Title',
                            'body' => 'iOS Body',
                        ],
                    ],
                ],
            ],
            'webpush' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => 'Webpush Title',
                    'body' => 'Webpush Body'
                ],
            ],
            'fcm_options' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#fcmoptions
                'analytics_label' => 'some-analytics-label'
            ]
        ]);

        // $message = CloudMessage::withTarget('token', "dGslG6MHRRqHhflqZc4KlV:APA91bGpZTtPboCQ_9BloO5TvmgEo7wqBfYh_7fyGnxnfDutH0hWqWH1JHHH25JJO1DImRil68mVjWeNc6wN75CGcZOA1SP6WWazHI8b3r_l_BQ9dt3M5mm_g7lYvX2atLykeKn14z8w")
        //     ->withNotification(['title' => 'My title', 'body' => 'My Body']);

        $message = CloudMessage::withTarget('topic', "all")
            ->withNotification(['title' => 'My title', 'body' => 'My Body']);

        // $messaging->send($message);

  


        // $client = new Client();
        // $resp = $client->request('POST', "https://fcm.googleapis.com/v1/projects/gi1-info-app/messages:send", [
        //     'headers' => [
        //         'Authorization' =>'Bearer c9afc9a63f4b178598d17edfa08d92a9f1e723b6',
        //         'Content-Type' => 'application/json'
        //     ],
        //     'form_params' => [
        //         "message" => [
        //             "topic" => "all",
        //             "notification" => [
        //                 "title" => "Testing Message",
        //                 "body" => "This is a Test notification from the server."
        //             ],
        //             "data" => [
        //                 "uid" => "test"
        //             ],
        //             "android" => [
        //                 "notification" => [
        //                     "body" => "This is a Test notification from the server"
        //                 ]
        //             ],
        //             "apns" => [
        //                 "payload" => [
        //                     "aps" => [
        //                         "category" => "Gi1 Prime App"
        //                     ]
        //                 ]
        //             ]

        //         ]
        //     ]
        // ]);

        // return response()->json($resp);

    }
}
