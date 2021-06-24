<?php

namespace App\Repositories;

use App\Interfaces\ProductImageInterface;
use App\Product;
use App\ProductImage;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductImageRepository implements ProductImageInterface {
    use ResponseAPI;

    public function deleteByProduct(Product $product) {
        $pimg = ProductImage::where('product_id',$product->id);
        if(!$pimg) return $this->error('Image not found');

        if(!$pimg->count()) return $this->error('Permission denied');

        $pimg->delete();
    }

    public function delete($id) {
        DB::beginTransaction();
        try {
            $pimg = ProductImage::find($id);
            if(!$pimg) return $this->error('Image not found');

            $product = Product::where('id', $pimg->product_id)->where('user_id', Auth::user()->id)->first();
            if(!$product) return $this->error('Permission denied');

            $pimg->delete();

            DB::commit();

            return $this->success('Image successfully deleted',[]);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}