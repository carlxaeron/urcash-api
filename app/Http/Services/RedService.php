<?php

namespace App\Http\Services;

use App\Invoice;
use App\Product;
use App\PurchaseItem;
use App\User;
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

    public function purchase($req) {
        if($req instanceof Invoice) {
            $transid = $req->id;
            $uid = $req->user_id;
            $user = User::find($uid);
            $acctno = $user->data['RED_DATA_FROM_API']['accountno'] ?? false;
            $date = urlencode($req->created_at);
            foreach($req->data['CHECKOUT_ITEMS__items'] as $items) {
                $prodid = $items['product'];
                $product = Product::find($prodid);
                $amt = $product->price;
                $qty = $items['qty'];
                $URL = "https://myaccount.redinc.net/b2bapi/?trans=purchase&transid=$transid&u_id=$uid&acctno=$acctno&prod_id=$prodid&qty=$qty&amount=$amt&payment_gateway=pio&purchase_dt=$date&transdate=$date&security_key=".(sha1('**pre**'.$transid.($req->created_at).'**sup**'));
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

                $resp = (array) json_decode($response,true);
                if($resp['status'] == 'error') return (array) json_decode($response,true);
            }
        }
        elseif($req instanceof PurchaseItem) {

        }
    }

    public function link(User $user, Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $transid = $user->id;
        $URL = "https://myaccount.redinc.net/b2bapi/?trans=link&transid=$transid&u_id=$transid&redusername=".$request->username."&redpassword=".$request->password."&transdate=".urlencode($date)."&security_key=".(sha1('**pre**'.$transid.($date).'**sup**'));

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