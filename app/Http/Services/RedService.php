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

    public function register($req) {
        $request = $req->toArray();
        $refCode = $request['data'] && isset($request['data']['RED_REF_CODE']) ? $request['data']['RED_REF_CODE'] : '';
        $transid = time();
        $URL = "https://myaccount.redinc.net/b2bapi/?trans=reg&transid=".$transid."&u_id=".$request['id']."&refcode=$refCode&fname=".$request['first_name']."&lname=".$request['last_name']."&email=".$request['email']."&passwd=".$req->password."&date_reg=".urlencode($req->created_at)."&transdate=".urlencode($req->created_at)."&security_key=".(sha1('**pre**'.$transid.($req->created_at).'**sup**'));

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $URL,
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