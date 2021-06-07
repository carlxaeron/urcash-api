<?php

namespace App\Repositories;

use App\Price;
use App\Shop;
use App\Interfaces\PriceInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PriceRepository implements PriceInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllPrices()
    {
        try {
            $prices = Price::all();

            if ($prices->count() < 1) {
                return $this->error("Prices not found", 404);
            }

            return $this->success("All prices", $prices);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPriceById($id)
    {
        try {
            $price = Price::find($id);

            if (!$price) return $this->error("Price not found", 404);

            return $this->success("Price detail", $price);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updatePrice(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $price = Price::find($id);

            if (!$user) {
                return $this->error("You are not authenticated", 401);
            } elseif (!$price) {
                return $this->error("Price not found", 404);
            }

            $inputs = [
                'shop_id' => $request->shop_id,
                'product_id' => $request->product_id,
                'price' => $request->price
            ];
            $rules = [
                'shop_id' => 'required|exists:shops,id',
                'product_id' => 'nullable|exists:products,id',
                'price' => 'nullable|numeric|min:0'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $shop = Shop::find($request->shop_id);

            if ($shop->user_id != $user->id) { // Ensure that merchants only update their own prices and not others
                return $this->error("Unauthorized access", 403);
            } elseif ($request->has('product_id')) {
                $price->product_id = $request->product_id;
            } elseif ($request->has('price')) {
                $price->price = $request->price;
            }
            $price->save();

            return $this->success("Price updated", $price);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deletePrice($id)
    {
        DB::beginTransaction();
        try {
            $price = Price::find($id);

            if (!$price) return $this->error("Price not found", 404);

            $price->delete();

            DB::commit();
            return $this->success("Price deleted", $price);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createPrice(Request $request)
    {
        try {
            $inputs = [
                'shop_id' => $request->shop_id,
                'product_id' => $request->product_id,
                'price' => $request->price
            ];
            $rules = [
                'shop_id' => 'required|exists:shops,id',
                'product_id' => 'required|exists:products,id',
                'price' => 'required|numeric|min:0'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $price = Price::create([
                'shop_id' => $request->shop_id,
                'product_id' => $request->product_id,
                'price' => $request->price
            ]);

            return $this->success("Price created", $price);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
