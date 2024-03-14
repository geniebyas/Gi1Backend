<?php

use App\Models\UsersSetting;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

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
