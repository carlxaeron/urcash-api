<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(ProductInterface $productInterface) {
        $this->productInterface = $productInterface;
    }

    /**
     * Get all products
     */
    public function index() {
        return $this->productInterface->getAllProducts();
    }

    public function indexV1() {
        return $this->productInterface->getAllProductsV1();
    }

    public function allProducts() {
        return $this->productInterface->getAllProductsAdmin();
    }

    public function getRelatedProducts(Request $request) {
        return $this->productInterface->getRelatedProducts($request);
    }

    public function getSearchProducts(Request $request) {
        return $this->productInterface->getSearchProducts($request);
    }

    public function myProducts() {
        return $this->productInterface->getUserProducts();
    }

    /**
     * Get product by ID
     */
    public function show($id) {
        return $this->productInterface->getProductById($id);
    }

    /**
     * Get all distinct products by name
     */
    public function showByName() {
        return $this->productInterface->getProductNames();
    }

    /**
     * Get all distinct products by manufacturer_name
     */
    public function showByManufacturer() {
        return $this->productInterface->getManufacturers();
    }

    /**
     * Get all products whose is_verified is False
     */
    public function showUnverifiedProducts() {
        return $this->productInterface->getUnverifiedProducts();
    }

    /**
     * Checkout products
     */
    public function checkoutProducts(Request $request) {
        return $this->productInterface->checkoutProducts($request);
    }

    /**
     * Checkout products
     */
    public function checkoutProductsV1(Request $request) {
        return $this->productInterface->checkoutProductsV1($request);
    }

    /**
     * Search products based on query
     */
    public function searchProducts(Request $request) {
        return $this->productInterface->searchProducts($request);
    }

    /**
     * Search product by ean
     */
    public function searchProductByEan(Request $request) {
        return $this->productInterface->searchProductByEan($request);
    }

    /**
     * Search products by manufacturer_name
     */
    public function searchProductsByManufacturer(Request $request) {
        return $this->productInterface->searchProductsByManufacturer($request);
    }

    /**
     * Update product
     */
    public function update(Request $request, $id) {
        return $this->productInterface->updateProduct($request, $id);
    }
    public function updateV1(Request $request, $id) {
        return $this->productInterface->updateProductV1($request, $id);
    }

    /**
     * Delete product
     */
    public function delete($id) {
        return $this->productInterface->deleteProduct($id);
    }
    public function deleteV1($id) {
        return $this->productInterface->deleteProductV1($id);
    }

    /**
     * Create product
     */
    public function create(Request $request) {
        return $this->productInterface->createProduct($request);
    }
    public function createV1(Request $request) {
        return $this->productInterface->createProductV1($request);
    }

    public function verifyProduct(Request $request) {
        return $this->productInterface->verifyProduct($request);
    }

    public function rejectProduct(Request $request) {
        return $this->productInterface->rejectProduct($request);
    }
    
    public function setProductLike(Request $request) {
        return $this->productInterface->setProductLike($request);
    }

    public function getUserProductLike() {
        return $this->productInterface->getUserProductLike();
    }
}
