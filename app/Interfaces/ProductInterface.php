<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ProductInterface
{
    /**
     * Get all products
     *
     * @method  GET api/products
     * @access  public
     */
    public function getAllProducts();

    public function getAllProductsV1();

    public function getRejectedProducts();

    public function getAllProductsAdmin();

    public function getRelatedProducts(Request $request);

    public function getSearchProducts(Request $request);

    public function getUserProducts();

    /**
     * Get product by ID
     *
     * @param   integer $id
     * @method  GET api/products/{id}
     * @access  public
     */
    public function getProductById($id);

    /**
     * Get all distinct products by name
     *
     * @method  GET api/products/names
     * @access  public
     */
    public function getProductNames();

    /**
     * Get all distinct products by manufacturer_name
     *
     * @method  GET api/products/manufacturers
     * @access  public
     */
    public function getManufacturers();

    /**
     * Get all products whose is_verified is False
     *
     * @method  GET api/products/unverified
     * @access  public
     */
    public function getUnverifiedProducts();

    /**
     * Checkout products
     *
     * @param   Request $request
     * @method  POST    api/products/checkout
     * @access  public
     */
    public function checkoutProducts(Request $request);

    public function checkoutProductsV1(Request $request);

    /**
     * Search products based on query
     *
     * @param   Request $request
     * @method  POST    api/products/search/query
     * @access  public
     */
    public function searchProducts(Request $request);

    /**
     * Search product by ean
     *
     * @param   Request $request
     * @method  POST    api/products/search/ean
     * @access  public
     */
    public function searchProductByEan(Request $request);

    /**
     * Search products by manufacturer_name
     *
     * @param   Request $request
     * @method  POST    api/products/search/manufacturer
     * @access  public
     */
    public function searchProductsByManufacturer(Request $request);

    /**
     * Update product
     *
     * @param   Request $request, integer $id
     * @method  POST    api/products/{id}/update
     * @access  public
     */
    public function updateProduct(Request $request, $id);
    public function updateProductV1(Request $request, $id);

    /**
     * Delete product
     *
     * @param   integer $id
     * @method  DELETE  api/products/{id}/delete
     * @access  public
     */
    public function deleteProduct($id);
    public function deleteProductV1($id);

    /**
     * Create product
     *
     * @param   Request $request
     * @method  POST    api/products/create
     * @access  public
     */
    public function createProduct(Request $request);

    public function createProductV1(Request $request);

    public function verifyProduct(Request $request);

    public function rejectProduct(Request $request);

    public function getResubmittedProducts();
    
    public function resubmitProduct(Request $request);

    public function setProductLike(Request $request);

    public function getUserProductLike();
}
