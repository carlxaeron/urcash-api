<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ShopInterface
{
    /**
     * Get all shops
     *
     * @method  GET api/shops
     * @access  public
     */
    public function getAllShops();

    /**
     * Get shop by ID
     *
     * @param   integer $id
     * @method  GET api/shops/{id}
     * @access  public
     */
    public function getShopById($id);

    /**
     * Get all distinct shops by reg_bus_name
     *
     * @method  GET api/shops/merchant_names
     * @access  public
     */
    public function getMerchantNames();

    /**
     * Get unverified products of a shop by shop ID
     *
     * @param   integer $id
     * @method  GET api/shops/{id}/products/unverified
     * @access  public
     */
    public function getUnverifiedProductsByShop($id);

    /**
     * Get all shops whose is_verified is False
     *
     * @method  GET api/shops/unverified
     * @access  public
     */
    public function getUnverifiedMerchants();

    /**
     * Verify merchant using documents submitted
     *
     * @param   Request $request
     * @method  POST    api/shops/merchant_verification
     * @access  public
     */
    public function merchantVerification(Request $request);

    /**
     * Update shop
     *
     * @param   Request $request, integer $id
     * @method  POST    api/shops/{id}/update
     * @access  public
     */
    public function updateShop(Request $request, $id);

    /**
     * Delete shop
     *
     * @param   integer $id
     * @method  DELETE  api/shops/{id}/delete
     * @access  public
     */
    public function deleteShop($id);

    /**
     * Create shop
     *
     * @param   Request $request
     * @method  POST    api/shops/create
     * @access  public
     */
    public function createShop(Request $request);

      /**
     * find shop
     *
     * @param  $id
     * @method  GET   api/find/shops/{id}
     * @access  public
     */
    public function findShop($id);
}
