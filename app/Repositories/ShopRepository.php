<?php

namespace App\Repositories;

use App\Address;
use App\Price;
use App\Product;
use App\Shop;
use App\User;
use App\VerificationRequest;
use App\Interfaces\ShopInterface;
use App\Repositories\UserRepository;
use App\Repositories\VerificationRequestRepository;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShopRepository implements ShopInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $document_choices = ['BIR Registration Certificate', 'DTI Permit', 'Mayor\'s Permit'];

    public function getAllShops()
    {
        try {
            $shops = Shop::all();

            if ($shops->count() < 1) {
                return $this->error("Shops not found", 404);
            }

            return $this->success("All shops", $shops);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getShopById($id)
    {
        try {
            $shop = Shop::find($id);

            if (!$shop) return $this->error("Shop not found", 404);

            return $this->success("Shop detail", $shop);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getMerchantNames()
    {
        try {
            $merchants = Shop::select('reg_bus_name')->distinct()->get();
            $merchant_names = array();

            foreach ($merchants as $merchant) array_push($merchant_names, $merchant->reg_bus_name);
            sort($merchant_names,SORT_STRING | SORT_FLAG_CASE);

            return $this->success("All merchant names", $merchant_names);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUnverifiedProductsByShop($id)
    {
        try {
            $unverified_products = array();
            $shop = Shop::find($id);
            $merchant_name = $shop->reg_bus_name;
            $products = Price::where('shop_id', '=', $id)->get();

            for ($i = 0; $i < $products->count(); $i++) {
                $product = Product::find($products[$i]['product_id']);

                if ($product->is_verified == False) {
                    $unverified_products[$i] = $product;
                }
            }

            if (count($unverified_products) < 1) {
                return $this->error("Merchant $merchant_name has currently no unverified products", 404);
            }

            return $this->success("All unverified products of merchant $merchant_name", $unverified_products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUnverifiedMerchants()
    {
        try {
            $merchants_array = array();
            $merchants = Shop::where('is_verified', '=', False)->get();

            if ($merchants->count() < 1) {
                return $this->error("No pending unverified merchants", 404);
            }

            for ($i = 0; $i < $merchants->count(); $i++) {
                $verification_request = VerificationRequest::where('type', '=', 'merchant_verification')
                    ->where('user_id', '=', $merchants[$i]['user_id'])->first();
                $address = Address::find($merchants[$i]['address_id']);
                $user = User::find($merchants[$i]['user_id']);

                if ($verification_request) {
                    $merchants_array[$i]['id'] = $merchants[$i]['id'];
                    $merchants_array[$i]['reg_bus_name'] = $merchants[$i]['reg_bus_name'];
                    $merchants_array[$i]['dti'] = $merchants[$i]['dti'];
                    $merchants_array[$i]['bir_reg_cert'] = $merchants[$i]['bir_reg_cert'];
                    $merchants_array[$i]['mayors_permit'] = $merchants[$i]['mayors_permit'];
                    $merchants_array[$i]['is_verified'] = $merchants[$i]['is_verified'];
                    $merchants_array[$i]['created_at'] = $merchants[$i]['created_at'];
                    $merchants_array[$i]['updated_at'] = $merchants[$i]['updated_at'];
                    $merchants_array[$i]['verification_request_id'] = $verification_request->id;
                    $merchants_array[$i]['document_submitted'] = $verification_request->document;
                    $merchants_array[$i]['image_path'] = str_replace("\\\\", "\\", $verification_request->uploaded_file_path);
                    $merchants_array[$i]['business_address_id'] = $address->id;
                    $merchants_array[$i]['business_address'] = $address->complete_address;
                    $merchants_array[$i]['business_street'] = $address->street;
                    $merchants_array[$i]['business_barangay'] = $address->barangay;
                    $merchants_array[$i]['business_city'] = $address->city;
                    $merchants_array[$i]['business_province'] = $address->province;
                    $merchants_array[$i]['business_country'] = $address->country;
                    $merchants_array[$i]['first_name'] = $user->first_name;
                    $merchants_array[$i]['middle_name'] = $user->middle_name;
                    $merchants_array[$i]['last_name'] = $user->last_name;
                    $merchants_array[$i]['mobile_number'] = $user->mobile_number;
                    $merchants_array[$i]['birthdate'] = $user->birthdate;
                    $merchants_array[$i]['email'] = $user->email;
                    $merchants_array[$i]['is_locked'] = $user->is_locked;
                    $merchants_array[$i]['profile_picture'] = $user->profile_picture;

                }
            }

            return $this->success("All unverified merchants with merchant verification requests", array(
                "count" => count($merchants_array),
                "merchants" => array_values($merchants_array) // Re-index array
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function merchantVerification(Request $request)
    {
        try {
            $user_repository = new UserRepository();
            $get_user = $user_repository->getUserById($request->user_id);

            if ($get_user->getData()->statusCode == 404) {
                return $this->error($get_user->getData()->message);
            }

            $user = $get_user->getData()->results;
            $verification_request = VerificationRequest::where('type', '=', 'merchant_verification')
                ->where('user_id', '=', $user->id)->first();

            if ($verification_request) {
                return $this->error("You already have a pending merchant verification request", 429);
            }

            $shop = Shop::where('user_id', '=', $user->id)->first();

            if (!$shop) {
                return $this->error("Shop not found for this user", 404);
            } elseif ($shop->is_verified == True) {
                return $this->error("Merchant is already verified");
            }

            $inputs = [
                'user_id' => $request->user_id,
                'document' => $request->document,
                'select_file' => $request->file('select_file')
            ];
            $rules = [
                'user_id' => 'required|exists:users,id',
                'document' => ['required', Rule::in($this->document_choices)],
                'select_file' => 'required|image|mimes:jpeg,jpg,gif,png|max:3072'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $request->type = 'merchant_verification';
            $verification_request_repository = new VerificationRequestRepository();
            $verification_request = $verification_request_repository->createVerificationRequest($request);

            if ($verification_request->getData()->statusCode == 500) {
                return $this->error($verification_request->getData()->message);
            }

            return $this->success("Merchant verification request submitted!", $verification_request->getData()->results);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateShop(Request $request, $id)
    {
        try {
            $shop = Shop::find($id);

            if (!$shop) return $this->error("Shop not found", 404);

            $inputs = [
                'address_id' => $request->address_id,
                'user_id' => $request->user_id,
                'reg_bus_name' => $request->reg_bus_name,
                'dti' => $request->dti,
                'bir_reg_cert' => $request->bir_reg_cert,
                'mayors_permit' => $request->mayors_permit
            ];
            $rules = [
                'address_id' => 'nullable|exists:addresses,id',
                'user_id' => 'nullable|exists:users,id',
                'reg_bus_name' => 'nullable',
                'dti' => 'nullable|max:8',
                'bir_reg_cert' => 'nullable|max:13',
                'mayors_permit' => 'nullable|max:16'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->has('address_id')) {
                $shop->address_id = $request->address_id;
            }
            if ($request->has('user_id')) {
                $shop->user_id = $request->user_id;
            }
            if ($request->has('reg_bus_name')) {
                $shop->reg_bus_name = $request->reg_bus_name;
            }
            if ($request->has('dti')) {
                $shop->dti = $request->dti;
            }
            if ($request->has('bir_reg_cert')) {
                $shop->bir_reg_cert = $request->bir_reg_cert;
            }
            if ($request->has('mayors_permit')) {
                $shop->mayors_permit = $request->mayors_permit;
            }
            $shop->save();

            return $this->success("Shop updated", $shop);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteShop($id)
    {
        DB::beginTransaction();
        try {
            $shop = Shop::find($id);

            if (!$shop) return $this->error("Shop not found", 404);

            $shop->delete();

            DB::commit();
            return $this->success("Shop deleted", $shop);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createShop(Request $request)
    {
        try {
            $inputs = [
                'address_id' => $request->address_id,
                'user_id' => $request->user_id,
                'reg_bus_name' => $request->reg_bus_name,
                'dti' => $request->dti,
                'bir_reg_cert' => $request->bir_reg_cert,
                'mayors_permit' => $request->mayors_permit
            ];
            $rules = [
                'address_id' => 'required|exists:addresses,id',
                'user_id' => 'required|exists:users,id',
                'reg_bus_name' => 'required',
                'dti' => 'required_without_all:bir_reg_cert,mayors_permit|max:8',
                'bir_reg_cert' => 'required_without_all:dti,mayors_permit|max:13',
                'mayors_permit' => 'required_without_all:dti,bir_reg_cert|max:16'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $shop = Shop::create([
                'address_id' => $request->address_id,
                'user_id' => $request->user_id,
                'reg_bus_name' => $request->reg_bus_name,
                'dti' => $request->dti,
                'bir_reg_cert' => $request->bir_reg_cert,
                'mayors_permit' => $request->mayors_permit,
                'is_verified' => False
            ]);

            return $this->success("Shop created", $shop);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function findShop($id)
    {
        try{
            $shop = Shop::find($id);

            if(!$shop)
            {
                return $this->error('Shop not found', 404);
            }
            $address = Address::find($shop->address_id);


            $shopDetails = array(
                'shop' => $shop,
                'address' =>  $address
            );

            return  $this->success('Shop details', array('shop_details'=>$shopDetails));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
