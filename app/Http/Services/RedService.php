<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class RedService {
    public static $ERR_SUCCESS_NOT_YET_REGISTERED = 'ERR_SUCCESS_NOT_YET_REGISTERED';

    public function login(Request $request) {
        $curl = curl_init();

        $POST = $request->all();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://myaccount.redinc.net/g2gapi/?trans=link&username='
        .$POST['email']
        .'&pw='
        .$POST['password']
        .'&security_key=UmVzdG9yZS4uLiBFbmhhbmNlLi4uIERlZmVuZC4uLg==',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return (array) json_decode($response,true);
    }
}