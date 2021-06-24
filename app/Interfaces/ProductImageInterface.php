<?php

namespace App\Interfaces;

use App\Product;
use App\ProductImage;

interface ProductImageInterface
{
    public function deleteByProduct(Product $product);

    public function delete($id);
}