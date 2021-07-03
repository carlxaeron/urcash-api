<?php

namespace App\Repositories;

use App\Category;
use App\Http\Resources\Product as ResourcesProduct;
use App\Price;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\Shop;
use App\User;
use App\VerificationRequest;
use App\Interfaces\ProductInterface;
use App\Mail\CheckoutProducts;
use App\Notifications\CheckoutProducts as NotificationsCheckoutProducts;
use App\ProductCategory;
use App\ProductImage;
use App\Repositories\PriceRepository;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements ProductInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllProducts()
    {
        try {
            $products = Product::all();

            if ($products->count() < 1) {
                return $this->error("Products not found", 404);
            }

            return $this->success("All Products", $products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllProductsV1()
    {
        try {
            $products = Product::with('owner')->verified();

            if ($products->count() < 1) {
                return $this->error("Products not found", 404);
            }

            if(request()->page) $products = $products->paginate(request()->per_page ?? 10);
            else $products = $products->get();

            return $this->success("All Products", new ResourcesProduct($products));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllProductsAdmin()
    {
        try {
            $products = Product::with('owner');

            if ($products->count() < 1) {
                return $this->error("Products not found", 404);
            }

            if(request()->page) $products = $products->paginate(request()->per_page ?? 10);
            else $products = $products->get();

            return $this->success("All Products", new ResourcesProduct($products));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getRelatedProducts(Request $request)
    {
        try {
            $products = Cache::remember('getRelatedProducts'.$request->limit, Carbon::now()->addDay(), function () use($request) {
                $products = Product::with('owner')->verified()->related($request->limit ? $request->limit : 20);
    
                if(request()->page) $products = $products->paginate(request()->per_page ?? 10);
                else $products = $products->get();
                
                return $products;
            });

            return $this->success('All Related Products.', new ResourcesProduct($products));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSearchProducts(Request $request)
    {
        try {
            $inputs = [
                'keyword' => $request->keyword,
            ];
            $rules = [
                'keyword' => 'required',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $products = Product::with('owner')->verified()->search($request->keyword);

            if(request()->page) $products = $products->paginate(request()->per_page ?? 10);
            else $products = $products->get();

            return $this->success('All Searched Products.', new ResourcesProduct($products));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUserProducts()
    {
        try {
            $products = Product::where('user_id',Auth::user()->id);

            if ($products->count() < 1) {
                return $this->error("Products not found", 404);
            }

            if(request()->page) $products = $products->paginate(request()->per_page ?? 10);
            else $products = $products->get();

            return $this->success("All Products", new ResourcesProduct($products));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getProductById($id)
    {
        try {
            $product = Product::with('owner')->find($id);

            if (!$product) return $this->error("Product not found", 404);

            return $this->success("Product detail", $product);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getProductNames()
    {
        try {
            $products = Product::select('name', 'ean')->distinct()->get();
            $product_names = array();

            for ($i = 0; $i < $products->count(); $i++) {
                $product_names[$i]['ean'] = $products[$i]['ean'];
                $product_names[$i]['name'] = $products[$i]['name'];
            }

            return $this->success("All products", $product_names);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getManufacturers()
    {
        try {
            $products = Product::select('manufacturer_name')->distinct()->get();
            $manufacturers = array();

            foreach ($products as $product) array_push($manufacturers, $product->manufacturer_name);
            sort($manufacturers,SORT_STRING | SORT_FLAG_CASE);

            return $this->success("All manufacturers", $manufacturers);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUnverifiedProducts()
    {
        try {
            $products_array = array();
            $products = Product::where('is_verified', '=', False)->get();

            if ($products->count() < 1) {
                return $this->error("No pending unverified products", 404);
            }

            for ($i = 0; $i < $products->count(); $i++) {
                $verification_request = VerificationRequest::where('product_id', '=', $products[$i]['id'])->first();

                $products_array[$i]['id'] = $products[$i]['id'];
                $products_array[$i]['sku'] = $products[$i]['sku'];
                $products_array[$i]['ean'] = $products[$i]['ean'];
                $products_array[$i]['name'] = $products[$i]['name'];
                $products_array[$i]['manufacturer_name'] = $products[$i]['manufacturer_name'];
                $products_array[$i]['variant'] = $products[$i]['variant'];
                $products_array[$i]['is_verified'] = $products[$i]['is_verified'];
                $products_array[$i]['created_at'] = $products[$i]['created_at'];
                $products_array[$i]['updated_at'] = $products[$i]['updated_at'];
                $products_array[$i]['verification_request_id'] = $verification_request->id;
            }

            return $this->success("All unverified products", array(
                "count" => $products->count(),
                "products" => $products_array
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function checkoutProductsV1(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'products' => $request->products,
            ];
            $rules = [
                'products' => 'required',
                'products.*.quantity' => 'required|integer',
                'products.*.product_id' => ['required', function($attr,$value,$fail) {
                    if(!Product::find($value)) $fail('Product ID #'.$value.' not exists.');
                }],
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());
            $user = Auth::user();
            $subtotal = 0;
            $batchCode = \Illuminate\Support\Str::uuid();
            foreach($request->products as $prod) {
                $product = Product::find($prod['product_id']);
                $p = PurchaseItem::create([
                    'product_id'=>$product->id,
                    'quantity'=>$prod['quantity'],
                    'user_id'=>$user->id,
                    'price'=>$product->prices->price,
                    'batch_code'=>$batchCode,
                    'data'=>1
                ]);
                $p->status = 'processing';
                $p->purchase_status = 'unpaid';
                $p->payment_method = 'COD';
                $p->save();
                $subtotal += ($product->prices->price * $prod['quantity']);
            }
            $purchase = PurchaseItem::with(['product.categories.category'])->where('batch_code', $batchCode)->get();

            $user->notify(new NotificationsCheckoutProducts($user, $purchase));
            
            DB::commit();

            return $this->success("Transaction complete", array(
                "products_purchased" => $request->products,
                "subtotal" => $subtotal,
                "transaction" => $purchase,
            ));
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), $e->getCode());
        }
        
    }

    public function checkoutProducts(Request $request)
    {
        try {
            $inputs = [
                'shop_id' => $request->shop_id,
                'customer_phone_number' => $request->customer_phone_number,
                'products' => $request->products,
            ];
            $rules = [
                'shop_id' => 'required|exists:shops,id',
                'customer_phone_number' => 'required|starts_with:09|digits:11',
                'products.*.quantity' => 'required|integer',
                'products.*.product_id' => 'required|exists:products,id',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $products = $request->products; // Get all key-value pairs and store in a var
            $subtotal = 0;
            $products_with_no_prices = array();
            $products_with_prices = array();

            for ($i = 0; $i < count($products); $i++) { // Check if some of the products do not have prices
                $get_price = Price::where('product_id', '=', $products[$i]['product_id'])->first();

                if (!$get_price) {
                    $product = Product::find($products[$i]['product_id']);
                    $products_with_no_prices[$i]['id'] = $product->id;
                    $products_with_no_prices[$i]['name'] = $product->name;
                } else {
                    array_push($products_with_prices, $products[$i]);
                }
            }

            if (count($products_with_no_prices) > 0) {
                return $this->success("Products with no prices", $products_with_no_prices, 500);
            }

            $purchase = Purchase::create([
                'shop_id' => $request->shop_id,
                'customer_mobile_number' => $request->customer_phone_number
            ]);

            for ($i = 0; $i < count($products_with_prices); $i++) {
                $get_price = Price::where('product_id', '=', $products_with_prices[$i]['product_id'])
                    ->where('shop_id', '=', $request->shop_id)->first()->price;
                $products_with_prices[$i]['price'] = $products_with_prices[$i]['quantity'] * $get_price;
                $subtotal += $products_with_prices[$i]['price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $products[$i]['product_id'],
                    'quantity' => $products[$i]['quantity']
                ]);
            }

            $shop = Shop::find($request->shop_id);
            $user = User::find($shop->user_id);

            Mail::to($user)->send(new CheckoutProducts($user, $purchase));

            return $this->success("Transaction complete", array(
                "products_purchased" => $request->products,
                "subtotal" => $subtotal,
                "transaction" => $purchase,
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchProducts(Request $request)
    {
        try {
            $search_query = $request->search_query;
            $products_all = $this->getAllProducts();

            if ($search_query == '' || $search_query == null) { // Return all records if search query is null
                return $this->success($products_all->getData()->message, array(
                    "e_wallets" => $products_all->getData()->results,
                    "count" => count($products_all->getData()->results)
                ));
            }

            $filter_products = Product::where('name', 'like', '%' .$search_query. '%')
                ->orWhere('manufacturer_name', 'like', '%' .$search_query. '%')
                ->orWhere('variant', 'like', '%' .$search_query. '%')->get();
            $results_count = $filter_products->count();

            if ($results_count < 1) {
                return $this->error("No results returned from your query", 404);
            } elseif ($results_count == 1) {
                $message = "Search returned 1 result";
            } else {
                $message = "Search returned $results_count results";
            }

            return $this->success($message, array(
                "products" => $filter_products,
                "count" => $results_count
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchProductByEan(Request $request)
    {
        try {
            $inputs = [
                'ean' => $request->ean,
                'shop_id' => $request->shop_id
            ];
            $rules = [
                'ean' => 'required|exists:products,ean',
                'shop_id' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $product = Product::where('ean', '=', $request->ean)->first();

            if (!$product) return $this->error("Product not found", 404);

            $price = Price::where('product_id', '=', $product->id)->where('shop_id', '=', $request->shop_id)->first();

            if ($product && !$price) {
               return $this->success("Do you want to add this product?", array(
                   'product_available' => true,
                   'price_available' => false,
                   'product_id' => $product->id
               ));
            }

            if ($product && $price) {
               return $this->success("Product detail", array(
                   'product_available' => true,
                   'price_available' => true,
                   'product' => $product,
                   'price' => $price
               ));
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchProductsByManufacturer(Request $request)
    {
        try {
            $inputs = ['manufacturer_name' => $request->manufacturer_name];
            $rules = ['manufacturer_name' => 'required|string|max:128'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $manufacturer = $request->manufacturer_name;
            $products = Product::where('manufacturer_name', '=', $manufacturer)->get();

            if ($products->count() < 1) {
                return $this->error("Products for $manufacturer not found", 404);
            }

            return $this->success("Products of $manufacturer", $products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) return $this->error("Product not found", 404);

            $inputs = [
                'shop_id' => $request->shop_id,
                'price' => $request->price,
                'sku' => $request->sku,
                'ean' => $request->ean,
                'name' => $request->name,
                'manufacturer_name' => $request->manufacturer_name,
                'variant' => $request->variant
            ];
            $rules = [
                'shop_id' => 'required|exists:shops,id',
                'price' => 'nullable|numeric|min:0',
                'name' => 'nullable',
                'manufacturer_name' => 'nullable',
                'variant' => 'nullable'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $get_price = Price::where('product_id', '=', $product->id)->where('shop_id', '=', $request->shop_id)->first();

            if ($request->has('price')) {
                $price_repository = new PriceRepository();
                $get_price = $price_repository->updatePrice($request, $get_price->id)->getData()->results;
            }
            if ($request->has('sku')) {
                $product->sku = $request->sku;
            }
            if ($request->has('ean')) {
                $product->ean = $request->ean;
            }
            if ($request->has('name')) {
                $product->name = $request->name;
            }
            if ($request->has('manufacturer_name')) {
                $product->manufacturer_name = $request->manufacturer_name;
            }
            if ($request->has('variant')) {
                $product->variant = $request->variant;
            }
            $product->save();

            return $this->success("Product updated", array(
                "product" => $product,
                "price" => $get_price->price
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function updateProductV1(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $product = Product::where('id',$id)->where('user_id',$user->id)->first();

            if (!$product) return $this->error("Product not found", 404);

            $inputs = [
                'price' => $request->price,
                'name' => $request->name,
                // 'image' =>  $request->image,
                // 'description' =>  $request->description,
            ];
            $rules = [
                'price' => 'required|numeric|min:0',
                'name' => 'required',
                // 'image' => 'required|max:5|array',
                // 'description' => 'required',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $get_price = Price::where('product_id', '=', $product->id)->first();

            if ($request->has('price')) {
                $price_repository = new PriceRepository();
                $get_price = $price_repository->updatePriceV1($request, $get_price->id)->getData()->results;
            }
            if ($request->has('sku')) {
                $product->sku = $request->sku;
            }
            if ($request->has('ean')) {
                $product->ean = $request->ean;
            }
            if ($request->has('name')) {
                $product->name = $request->name;
            }
            if ($request->has('manufacturer_name')) {
                $product->manufacturer_name = $request->manufacturer_name;
            }
            if ($request->has('variant')) {
                $product->variant = $request->variant;
            }
            $product->save();

            return $this->success("Product updated", array(
                "product" => $product,
                "price" => $get_price->price
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);

            if (!$product) return $this->error("Product not found", 404);

            $product->delete();

            DB::commit();
            return $this->success("Product deleted", $product);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function deleteProductV1($id)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $product = Product::where('id',$id)->where('user_id',$user->id)->first();

            if (!$product) return $this->error("Product not found", 404);

            app(PriceRepository::class)->deletePrice($id);

            $pimg = app(ProductImageRepository::class)->deleteByProduct($product);
            if($pimg) return $pimg;

            $product->delete();

            DB::commit();
            return $this->success("Product deleted", $product);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createProductV1(Request $request)
    {
        try {
            $inputs = [
                'price' => $request->price,
                'name' => $request->name,
                'image' =>  $request->image,
                'description' =>  $request->description,
                'categories' =>  $request->categories,
            ];
            $rules = [
                'price' => 'required|numeric|min:0',
                'name' => 'required',
                'image' => 'required|max:5|array',
                'description' => 'required',
                'categories' => ['required','array', function($attr, $value, $fail){
                    foreach($value as $v) {
                        if(!Category::find($v)) $fail("The category ID #$v does not exist.");
                    }
                }],
            ];
            $validation = Validator::make($inputs, $rules, [
                'image.max' => 'Maximum upload file reached.'
            ]);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = Auth::user();

            $inputs['is_verified'] = false;
            $inputs['user_id'] = $user->id;

            $product = Product::create($inputs);

            if ($product) {
                $price_repository = new PriceRepository();
                $request->product_id = $product->id;
                $price_repository->createPriceV1($request);

                foreach($request->image as $img) {
                    ProductImage::create(['filename'=>$img->store('image'),'product_id'=>$product->id]);
                }

                foreach($request->categories as $cat) {
                    ProductCategory::create(['product_id'=>$product->id,'category_id'=>$cat]);
                }

                $product = $product->with(['categories.category'])->find($product->id);
            }

            //     $product = Product::create([
            //         'sku' => $request->sku,
            //         'name' => $request->name,
            //         // 'variant' => $request->variant,
            //         'is_verified' => False,
            //     ]);
            //     $user = Auth::user();
            //     $request->product_id = $product->id;
            //     $price_repository = new PriceRepository();
            //     $price_repository->createPrice($request);

            //     $request->type = 'product_verification';
            //     $request->user_id = $user->id;
            //     $verification_request_repository = new VerificationRequestRepository();
            //     $verification_request = $verification_request_repository->createVerificationRequest($request);

            //     if ($verification_request->getData()->statusCode == 500) {
            //         return $this->error($verification_request->getData()->message);
            //     }

            //     return $this->success("Product created", array(
            //         "product" => $product,
            //         "verification_request" => $verification_request->getData()->results
            //     ));
            // }
            return $this->success("Product created", array(
                "product" => $product,
                // "verification_request" => $verification_request->getData()->results
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    public function createProduct(Request $request)
    {
        try {
            $inputs = [
                'shop_id' => $request->shop_id,
                'price' => $request->price,
                'sku' => $request->sku,
                'ean' => $request->ean,
                'name' => $request->name,
                'manufacturer_name' => $request->manufacturer_name,
                'variant' => $request->variant
            ];
            $rules = [
                'shop_id' => 'required|exists:shops,id',
                'price' => 'required|numeric|min:0',
                'sku' => 'nullable',
                'ean' => 'required',
                'name' => 'required',
                'manufacturer_name' => 'required',
                'variant' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $product = Product::where('ean', '=', $request->ean)->first();

            if (!$product) { // If ean does not yet exist, create it
                $product = Product::create([
                    'sku' => $request->sku,
                    'ean' => $request->ean,
                    'name' => $request->name,
                    'manufacturer_name' => $request->manufacturer_name,
                    'variant' => $request->variant,
                    'is_verified' => False,
                ]);
                $shop = Shop::find($request->shop_id);
                $user = User::find($shop->user_id);
                $request->product_id = $product->id;
                $price_repository = new PriceRepository();
                $price_repository->createPrice($request);

                $request->type = 'product_verification';
                $request->user_id = $user->id;
                $verification_request_repository = new VerificationRequestRepository();
                $verification_request = $verification_request_repository->createVerificationRequest($request);

                if ($verification_request->getData()->statusCode == 500) {
                    return $this->error($verification_request->getData()->message);
                }

                return $this->success("Product created", array(
                    "product" => $product,
                    "verification_request" => $verification_request->getData()->results
                ));
            } else { // Check if product has an existing price with the same merchant
                $product_has_price = Price::where('shop_id', '=', $request->shop_id)
                    ->where('product_id', '=', $product->id)->first();

                if ($product_has_price) {
                    return $this->error("You have already set a price for product with EAN $product->ean");
                }

                $request->product_id = $product->id;
                $price_repository = new PriceRepository();
                $price_repository->createPrice($request);

                return $this->success("Product created", $product);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function verifyProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'id' => $request->id,
            ];
            $rules = [
                'id' => 'required|exists:products,id'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $product = Product::unverified($request->id)->first();

            if(!$product) return $this->error('Product not exists or its already verified.');

            $product->is_verified = 1;
            $product->save();

            return $this->success("Product successfully verified.", $product);

            DB::commit();
        } catch (Exception $e)
        {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
