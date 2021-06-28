<?php

namespace App\Repositories;

use App\Address;
use App\Price;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\Shop;
use App\VoucherOrder;
use App\Http\Helper\Utils\CalculateSales;
use App\Http\Helper\Utils\GetTransactions;
use App\Http\Helper\Utils\SortAndSliceArrayByKey;
use App\Interfaces\PurchaseInterface;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseRepository implements PurchaseInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public $quantity = 0;
    public $total_sales = 0;

    public function getAllTransactions()
    {
        try {
            $purchases = Purchase::all();
            $voucher_orders_sold = VoucherOrder::where('status', '=', 'Verified')->get()->sum('amount');

            $get_transactions = new GetTransactions();
            $total_sales_lifetime = $get_transactions->getTransactions($purchases);

            return $this->success("All transactions", array(
                "total_sales" => $total_sales_lifetime,
                "total_transactions" => $purchases->count(),
                "total_voucher_orders_sold" => $voucher_orders_sold
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionById($id)
    {
        try {
            $purchase = Purchase::find($id);

            if (!$purchase) return $this->error("Purchase not found", 404);

            return $this->success("Purchase detail", $purchase);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsByShop($id)
    {
        try {
            $transactions = array();
            $shop = Shop::find($id);

            if (!$shop) return $this->error("Shop not found", 404);

            $purchases = Purchase::where('shop_id', '=', $shop->id)->orderBy('created_at', 'desc')->get();
            $transaction_count = $purchases->count();

            for ($i = 0; $i < $purchases->count(); $i++) {
                $shop = Shop::where('id', '=', $purchases[$i]['shop_id'])->first();
                $get_purchased_items = PurchaseItem::where('purchase_id', '=', $purchases[$i]['id'])->get();

                $transactions[$i]['id'] = $purchases[$i]['id'];
                $transactions[$i]['shop_id'] = $purchases[$i]['shop_id'];
                $transactions[$i]['customer_mobile_number'] = $purchases[$i]['customer_mobile_number'];
                $transactions[$i]['created_at'] = $purchases[$i]['created_at']->format('Y-m-d');
                $transactions[$i]['updated_at'] = $purchases[$i]['updated_at']->format('Y-m-d');

                $calculate_sales = new CalculateSales();
                $transactions[$i]['subtotal'] = $calculate_sales->calculateSubtotal($get_purchased_items, $shop->id);
            }

            if ($transaction_count < 1) {
                return $this->error("No transactions found for merchant $shop->reg_bus_name", 404);
            } else {
                if ($transaction_count == 1) {
                    return $this->success("Found $transaction_count transaction", $transactions);
                } else {
                    return $this->success("Found $transaction_count transactions", $transactions);
                }
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsToday()
    {
        try {
            $date_today = Carbon::today();
            $purchases_today = Purchase::where('created_at', 'like', $date_today->format('Y-m-d') . '%')->get();
            $voucher_orders_sold = VoucherOrder::where('status', '=', 'Verified')
                ->where('created_at', 'like', $date_today->format('Y-m-d') . '%')->get()->sum('amount');

            $get_transactions = new GetTransactions();
            $total_sales_today = $get_transactions->getTransactions($purchases_today);

            return $this->success("Today so far", array(
                "total_sales" => $total_sales_today,
                "total_transactions" => $purchases_today->count(),
                "total_voucher_orders_sold" => $voucher_orders_sold
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsTop5Cities()
    {
        try {
            $cities = array();
            $cities_processed = array();
            $shops = Shop::all();

            for ($i = 0; $i < $shops->count(); $i++) {
                $shop_address = Address::find($shops[$i]->address_id);
                $address_city = Address::where('city', '=', $shop_address->city)->get();

                // Check if city already exists in array, and if it does, do not process again
                if (!in_array($shop_address->city, $cities_processed)) {
                    array_push($cities_processed, $shop_address->city);
                    for ($j = 0; $j < $address_city->count(); $j++) {
                        $shop = Shop::where('address_id', '=', $address_city[$j]['id'])->first();

                        if ($shop) {
                            $purchase_transactions = Purchase::where('shop_id', '=', $shop->id)->get();

                            if ($purchase_transactions->count() > 0) {
                                for ($k = 0; $k < $purchase_transactions->count(); $k++) {
                                    $get_purchased_items = PurchaseItem::where('purchase_id', '=', $purchase_transactions[$k]['id'])->get();

                                    $calculate_sales = new CalculateSales();
                                    $this->total_sales += $calculate_sales->calculateSubtotal($get_purchased_items, $shop->id);
                                }
                                $cities[$i]['total_sales_of_city'] = round($this->total_sales, 2);
                            } else {
                                $cities[$i]['total_sales_of_city'] = 0;
                            }
                            $cities[$i]['city'] = $shop_address->city;
                        }
                    }
                }
                $this->total_sales = 0; // Reset for next city
            }
            $sort_and_slice = new SortAndSliceArrayByKey($cities, 'total_sales_of_city', 5);
            $top_5_cities = $sort_and_slice->sortAndSlice();

            return $this->success("Top 5 cities", array_values($top_5_cities));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsTop5Manufacturers()
    {
        try {
            $manufacturers = array();
            $manufacturers_processed = array();
            $products = Product::all();

            for ($i = 0; $i < $products->count(); $i++) {
                $get_manufacturers = Product::where('manufacturer_name', '=', $products[$i]['manufacturer_name'])->get();

                // Check if manufacturer_name already exists in array, and if it does, do not process again
                if (!in_array($products[$i]['manufacturer_name'], $manufacturers_processed)) {
                    array_push($manufacturers_processed, $products[$i]['manufacturer_name']);
                    for ($j = 0; $j < $get_manufacturers->count(); $j++) {
                        $get_product = PurchaseItem::where('product_id', '=', $get_manufacturers[$j]['id'])->get();

                        for ($k = 0; $k < $get_product->count(); $k++) {
                            $find_shop_id = Purchase::find($get_product[$k]['purchase_id'])->shop_id;

                            $get_price = Price::where('product_id', '=', $get_product[$k]['product_id'])
                                ->where('shop_id', '=', $find_shop_id)->first()->price;
                            $calculate_price = $get_product[$k]['quantity'] * $get_price;
                            $this->total_sales += $calculate_price;
                        }
                        $manufacturers[$i]['manufacturer_name'] = $get_manufacturers[$j]['manufacturer_name'];
                        $manufacturers[$i]['total_sales_of_manufacturer'] = round($this->total_sales, 2);
                    }
                    $this->total_sales = 0; // Reset for next manufacturer
                }
            }
            $sort_and_slice = new SortAndSliceArrayByKey($manufacturers, 'total_sales_of_manufacturer', 5);
            $top_5_manufacturers = $sort_and_slice->sortAndSlice();

            return $this->success("Top 5 manufacturers", array_values($top_5_manufacturers));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsTop5Merchants()
    {
        try {
            $merchants = array();
            $shops = Shop::all();

            for ($i = 0; $i < $shops->count(); $i++) {
                $find_transactions = Purchase::where('shop_id', '=', $shops[$i]->id)->get();

                if ($find_transactions) {
                    for ($j = 0; $j < $find_transactions->count(); $j++) {
                        $get_purchased_items = PurchaseItem::where('purchase_id', '=', $find_transactions[$j]['id'])->get();

                        $calculate_sales = new CalculateSales();
                        $this->total_sales += $calculate_sales->calculateSubtotal($get_purchased_items, $shops[$i]->id);
                    }
                    $merchants[$i]['shop_id'] = $shops[$i]->id;
                    $merchants[$i]['merchant_name'] = $shops[$i]->reg_bus_name;
                    $merchants[$i]['total_sales_of_merchant'] = round($this->total_sales, 2);
                }
                $this->total_sales = 0; // Reset for calculating total earnings of next merchant
            }
            $sort_and_slice = new SortAndSliceArrayByKey($merchants, 'total_sales_of_merchant', 5);
            $top_5_merchants = $sort_and_slice->sortAndSlice();

            return $this->success("Top 5 merchants", array_values($top_5_merchants));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTransactionsTop5Products()
    {
        try {
            $products = array();
            $product_ids_processed = array();
            $purchase_items = PurchaseItem::all();

            for ($i = 0; $i < $purchase_items->count(); $i++) {
                $get_product = PurchaseItem::where('product_id', '=', $purchase_items[$i]['product_id'])->get();

                // Check if product_id already exists in array, and if it does, do not process again
                if (!in_array($purchase_items[$i]['product_id'], $product_ids_processed)) {
                    array_push($product_ids_processed, $purchase_items[$i]['product_id']);
                    for ($j = 0; $j < $get_product->count(); $j++) {
                        $find_product = Product::find($get_product[$j]['product_id']);
                        $find_shop_id = Purchase::find($get_product[$j]['purchase_id'])->shop_id;

                        $products[$i]['product_id'] = $get_product[$j]['product_id'];
                        $products[$i]['product_name'] = $find_product->name;

                        $get_price = Price::where('product_id', '=', $get_product[$j]['product_id'])
                            ->where('shop_id', '=', $find_shop_id)->first()->price;
                        $calculate_price = $get_product[$j]['quantity'] * $get_price;
                        $this->total_sales += $calculate_price;
                    }
                    $products[$i]['total_sales_of_product'] = round($this->total_sales, 2);
                    $this->total_sales = 0; // Reset for next product
                }
            }
            $sort_and_slice = new SortAndSliceArrayByKey($products, 'total_sales_of_product', 5);
            $top_5_products = $sort_and_slice->sortAndSlice();

            return $this->success("Top 5 products", array_values($top_5_products));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getLatestTransactionsByValue($value)
    {
        try {
            $inputs = ['value' => $value];
            $rules = ['value' => 'required|integer|min:1'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $latest_transactions = array();
            $purchases_all = Purchase::all();

            if ($purchases_all->count() < 1) {
                return $this->error("Purchases not found", 404);
            }

            $get_purchases = Purchase::orderBy('created_at', 'desc')->take($value)->get();

            for ($i = 0; $i < $get_purchases->count(); $i++) {
                $get_purchased_items = PurchaseItem::where('purchase_id', '=', $get_purchases[$i]['id'])->get();
                $find_shop = Shop::find($get_purchases[$i]['shop_id']);

                $calculate_sales = new CalculateSales();
                $subtotal = $calculate_sales->calculateSubtotal($get_purchased_items, $find_shop->id);

                $find_address = Address::find($find_shop->address_id); // Address of merchant
                $latest_transactions[$i]['date'] = $get_purchases[$i]['created_at']->format('m/d/Y');
                $latest_transactions[$i]['time'] = $get_purchases[$i]['created_at']->format('H:i:s');
                $latest_transactions[$i]['merchant_name'] = $find_shop->reg_bus_name;
                $latest_transactions[$i]['customer_number'] = $get_purchases[$i]['customer_mobile_number'];
                $latest_transactions[$i]['cost'] = $subtotal;
                $latest_transactions[$i]['location'] = $find_address->city;
            }

            if ($value == 1) return $this->success("Latest transaction", $latest_transactions);

            return $this->success("Latest $value transactions", $latest_transactions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByManufacturer(Request $request)
    {
        try {
            $inputs = [
                'manufacturer_name' => $request->manufacturer_name,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'manufacturer_name' => 'required|exists:products,manufacturer_name',
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $from = $request->from. " 00:00:00";
            $to = $request->to. " 23:59:59";
            $transaction_count = 0;
            $manufacturer_products = Product::where('manufacturer_name', '=', $request->manufacturer_name)->get();

            for ($i = 0; $i < $manufacturer_products->count(); $i++) {
                $get_product = PurchaseItem::where('product_id', '=', $manufacturer_products[$i]['id'])
                    ->whereBetween('created_at', [$from, $to])->get();

                for ($j = 0; $j < $get_product->count(); $j++) {
                    $find_shop = Purchase::where('id', '=', $get_product[$j]['purchase_id'])
                        ->whereBetween('created_at', [$from, $to])->first();

                    $this->quantity += $get_product[$j]['quantity'];
                    $calculate_sales = new CalculateSales();
                    $this->total_sales += $calculate_sales->calculateSubtotalForPurchaseItemProducts(
                        $get_product[$j]['product_id'], $find_shop->shop_id, $get_product[$j]['quantity']);
                }
                $transaction_count += $get_product->count();
            }
            $manufacturer_info[0] = $this->storeSales($this->quantity, $this->total_sales, $transaction_count);
            $manufacturer_info[0]['manufacturer_name'] = $request->manufacturer_name;

            if ($manufacturer_info[0]['quantities_sold'] == 0 and $manufacturer_info[0]['total_sales_of_manufacturer'] == 0) {
                return $this->error("No sales for manufacturer $request->manufacturer_name between $from to $to", 404);
            }

            return $this->success("Sales for manufacturer $request->manufacturer_name", array("manufacturer" => $manufacturer_info));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByMerchant(Request $request)
    {
        try {
            $inputs = [
                'merchant_name' => $request->merchant_name,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'merchant_name' => 'required|exists:shops,reg_bus_name',
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $from = $request->from. " 00:00:00";
            $to = $request->to.  " 23:59:59";
            $shop = Shop::where('reg_bus_name', '=', $request->merchant_name)->first();
            $find_transactions = Purchase::where('shop_id', '=', $shop->id)->whereBetween('created_at', [$from, $to])->get();

            if ($find_transactions->count() > 0) {
                for ($i = 0; $i < $find_transactions->count(); $i++) {
                    $get_purchased_items = PurchaseItem::where('purchase_id', '=', $find_transactions[$i]['id'])
                        ->whereBetween('created_at', [$from, $to])->get();

                    for ($j = 0; $j < $get_purchased_items->count(); $j++) {
                        $this->quantity += $get_purchased_items[$j]['quantity'];
                    }
                    $calculate_sales = new CalculateSales();
                    $this->total_sales += $calculate_sales->calculateSubtotal($get_purchased_items, $shop->id);
                }
            } else {
                return $this->error("No sales for merchant $request->merchant_name between $from to $to", 404);
            }
            $merchant_info[0] = $this->storeSales($this->quantity, $this->total_sales, $find_transactions->count());
            $merchant_info[0]['shop_id'] = $shop->id;
            $merchant_info[0]['merchant_name'] = $shop->reg_bus_name;

            return $this->success("Sales for merchant $request->merchant_name", array("merchant" => $merchant_info));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByProduct(Request $request)
    {
        try {
            $inputs = [
                'ean' => $request->ean,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'ean' => 'required|exists:products,ean',
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $product = Product::where('ean', '=', $request->ean)->first();

            if (!$product) return $this->error("Product not found", 404);

            $from = $request->from. " 00:00:00";
            $to = $request->to. " 23:59:59";
            $get_product = PurchaseItem::where('product_id', '=', $product->id)
                ->whereBetween('created_at', [$from, $to])->get();

            if ($get_product->count() > 0) {
                for ($i = 0; $i < $get_product->count(); $i++) {
                    $find_shop = Purchase::find($get_product[$i]['purchase_id']);

                    $this->quantity += $get_product[$i]['quantity'];
                    $calculate_sales = new CalculateSales();
                    $this->total_sales += $calculate_sales->calculateSubtotalForPurchaseItemProducts(
                        $get_product[$i]['product_id'], $find_shop->shop_id, $get_product[$i]['quantity']);
                }
            } else {
                return $this->error("No sales for product $product->name between $from to $to", 404);
            }
            $product_info[0] = $this->storeSales($this->quantity, $this->total_sales, $get_product->count());
            $product_info[0]['product_name'] = $product->name;

            return $this->success("Sales for product $product->name", array("product" => $product_info));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByBarangay(Request $request)
    {
        try {
            $inputs = [
                'barangay' => $request->barangay,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'barangay' => 'required|exists:addresses,barangay',
                'from' => 'required|date',
                'to' => 'required|date'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $address_barangay = Address::where('barangay', '=', $request->barangay)->get();
            $calculate_sales = new CalculateSales();
            $sales = $calculate_sales->calculate($address_barangay, $request->barangay, 'barangay', $request->from, $request->to);

            if ($sales['merchants'] == null and $sales['total_sales'] == null) {
                return $this->error("No sales found for barangay $request->barangay", 404);
            }

            return $this->success("Sales for barangay $request->barangay", array(
                "total_sales" => $sales['total_sales'],
                "merchants" => array_values($sales['merchants'])
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByCity(Request $request)
    {
        try {
            $inputs = [
                'city' => $request->city,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'city' => 'required|exists:addresses,city',
                'from' => 'required|date',
                'to' => 'required|date'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $address_city = Address::where('city', '=', $request->city)->get();
            $calculate_sales = new CalculateSales();
            $sales = $calculate_sales->calculate($address_city, $request->city, 'city', $request->from, $request->to);

            if ($sales['merchants'] == null and $sales['total_sales'] == null) {
                return $this->error("No sales found for city $request->city", 404);
            }

            return $this->success("Sales for city $request->city", array(
                "total_sales" => $sales['total_sales'],
                "merchants" => array_values($sales['merchants'])
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function salesByProvince(Request $request)
    {
        try {
            $inputs = [
                'province' => $request->province,
                'from' => $request->from,
                'to' => $request->to
            ];
            $rules = [
                'province' => 'required|exists:addresses,province',
                'from' => 'required|date',
                'to' => 'required|date'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $address_province = Address::where('province', '=', $request->province)->get();
            $calculate_sales = new CalculateSales();
            $sales = $calculate_sales->calculate($address_province, $request->province, 'province', $request->from, $request->to);

            if ($sales['merchants'] == null and $sales['total_sales'] == null) {
                return $this->error("No sales found for province $request->province", 404);
            }

            return $this->success("Sales for province $request->province", array(
                "total_sales" => $sales['total_sales'],
                "merchants" => array_values($sales['merchants'])
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function storeSales ($quantity_sold, $total_sales, $total_transactions) // NO ROUTE
    {
        $sales_info['quantities_sold'] = $quantity_sold;
        $sales_info['total_sales_of_manufacturer'] = round($total_sales, 2);
        $sales_info['total_transactions'] = $total_transactions;

        return $sales_info;
    }
}
