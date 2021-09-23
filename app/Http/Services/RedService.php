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
            $transid = time();
            $uid = $req->user_id;
            $user = User::find($uid);
            $acctno = $req->data['requests']['red_account'] ?? $user->data['RED_DATA_FROM_API']['accountno'] ?? $user->data['RED_DATA']['accountno'] ?? false;
            $date = urlencode($req->created_at);
            $data = $req->data;
            foreach($req->data['CHECKOUT_ITEMS__items'] as $keyitems=>$items) {
                $prodid = $items['product'];
                $product = Product::find($prodid);
                $amt = $product->price;
                $company_price = $product->company_price;
                $qty = $items['qty'];
                $URL = "https://myaccount.redinc.net/b2bapi/?trans=purchase&transid=$transid&u_id=$uid&acctno=$acctno&prod_id=$prodid&qty=$qty&amount=$company_price&srp=$amt&payment_gateway=pio&purchase_dt=$date&transdate=$date&security_key=".(sha1('**pre**'.$transid.($req->created_at).'**sup**'));
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
                else {
                    $data['CHECKOUT_ITEMS__items_response'][$keyitems] = $resp;
                }
            }
            $req->data = $data;
            $req->save();
        }
        elseif($req instanceof PurchaseItem) {
            $transid = time();
            $uid = $req->user_id;
            $user = User::find($uid);
            $date = urlencode($req->created_at);
            $prodid = $req->product_id;
            $product = Product::find($prodid);
            $amt = $product->price;
            $company_price = $product->company_price;
            $qty = $req->quantity;
            $acctno = $req->data['requests']['red_account'] ?? $user->data['RED_DATA_FROM_API']['accountno'] ?? $user->data['RED_DATA']['accountno'] ?? false;
            $URL = "https://myaccount.redinc.net/b2bapi/?trans=purchase&transid=$transid&u_id=$uid&acctno=$acctno&prod_id=$prodid&qty=$qty&amount=$company_price&srp=$amt&payment_gateway=pio&purchase_dt=$date&transdate=$date&security_key=".(sha1('**pre**'.$transid.($req->created_at).'**sup**'));
            $curl = curl_init();
            $data = $req->data;
            
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
            $data['CHECKOUT_ITEMS__response'] = $resp;
            $req->data = $data;
            $req->save();
        }
    }

    public function purchasePointsOnRed(User $user, $request)
    {
        $transid = time();
        $userid = $user->id;
        $acctno = $request->red_acct;
        $pts = $request->points;
        $_date = date('Y-m-d H:i:s');
        $date = urlencode($_date);
        $URL = "https://myaccount.redinc.net/b2bapi/?trans=purchasepoints&transid=$transid&u_id=$userid&acctno=$acctno&pts=$pts&payment_gateway=pio&purchase_dt=$date&transdate=$date&security_key=".(sha1('**pre**'.$transid.($_date).'**sup**'));
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

    public function link(User $user, Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $userid = $user->id;
        $transid = time();
        $URL = "https://myaccount.redinc.net/b2bapi/?trans=link&transid=$transid&u_id=$userid&redusername=".$request->username."&redpassword=".$request->password."&transdate=".urlencode($date)."&security_key=".(sha1('**pre**'.$transid.($date).'**sup**'));
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

    public function getInfo(User $user)
    {
        $transid = time();
        $date = date('Y-m-d H:i:s');
        $userid = $user->id;
        $URL = "https://myaccount.redinc.net/b2bapi/?trans=getrcpts&transid=$transid&u_id=$userid&transdate=".urlencode($date)."&security_key=".(sha1('**pre**'.$transid.($date).'**sup**'));
        
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

    public function getAccounts(User $user)
    {
        $transid = time();
        $userid = $user->id;
        $date = date('Y-m-d H:i:s');

        $URL = "https://myaccount.redinc.net/b2bapi/?trans=linkedaccts&transid=$transid&u_id=$userid&transdate=".urlencode($date)."&security_key=".(sha1('**pre**'.$transid.($date).'**sup**'));

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

    public function getListPackages()
    {
        $transid = time();
        $date = date('Y-m-d H:i:s');

        $URL = "https://myaccount.redinc.net/b2bapi/?trans=packages&transid=$transid&transdate=".urlencode($date)."&security_key=".(sha1('**pre**'.$transid.($date).'**sup**'));

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