<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ShopInterface;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $shopInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(ShopInterface $shopInterface) {
        $this->shopInterface = $shopInterface;
    }

    /**
     * Get all shops
     */
    public function index() {
        return $this->shopInterface->getAllShops();
    }

    /**
     * Get shop by ID
     */
    public function show($id) {
        return $this->shopInterface->getShopById($id);
    }

    /**
     * Get all distinct shops by reg_bus_name
     */
    public function showByRegBusName() {
        return $this->shopInterface->getMerchantNames();
    }

    /**
     * Get unverified products of a shop by shop ID
     */
    public function showUnverifiedProductsByShop($id) {
        return $this->shopInterface->getUnverifiedProductsByShop($id);
    }

    /**
     * Get all shops whose is_verified is False
     */
    public function showUnverifiedMerchants() {
        return $this->shopInterface->getUnverifiedMerchants();
    }

    /**
     * Verify merchant using documents submitted
     */
    public function merchantVerification(Request $request) {
        return $this->shopInterface->merchantVerification($request);
    }

    /**
     * Update shop
     */
    public function update(Request $request, $id) {
        return $this->shopInterface->updateShop($request, $id);
    }

    /**
     * Delete shop
     */
    public function delete($id) {
        return $this->shopInterface->deleteShop($id);
    }

    /**
     * Create shop
     */
    public function create(Request $request) {
        return $this->shopInterface->createShop($request);
    }

     /**
     * Find shop
     */
    public function findShop($id) {
        return $this->shopInterface->findShop($id);
    }
}
