<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PurchaseInterface;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $purchaseInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(PurchaseInterface $purchaseInterface) {
        $this->purchaseInterface = $purchaseInterface;
    }

    /**
     * Get all purchase transactions
     */
    public function index() {
        return $this->purchaseInterface->getAllTransactions();
    }

    /**
     * Get purchase by ID
     */
    public function show($id) {
        return $this->purchaseInterface->getTransactionById($id);
    }

    /**
     * Get purchase transactions of a shop by ID
     */
    public function showTransactionsByShop($id) {
        return $this->purchaseInterface->getTransactionsByShop($id);
    }

    /**
     * Get all purchase transactions for current day
     */
    public function showTransactionsToday() {
        return $this->purchaseInterface->getTransactionsToday();
    }

    /**
     * Get top 5 cities of products for purchase transactions
     */
    public function showTransactionsTop5Cities() {
        return $this->purchaseInterface->getTransactionsTop5Cities();
    }

    /**
     * Get top 5 manufacturers of products for purchase transactions
     */
    public function showTransactionsTop5Manufacturers() {
        return $this->purchaseInterface->getTransactionsTop5Manufacturers();
    }

    /**
     * Get top 5 merchants of products for purchase transactions
     */
    public function showTransactionsTop5Merchants() {
        return $this->purchaseInterface->getTransactionsTop5Merchants();
    }

    /**
     * Get top 5 products for purchase transactions
     */
    public function showTransactionsTop5Products() {
        return $this->purchaseInterface->getTransactionsTop5Products();
    }

    /**
     * Get latest purchase transactions by value
     */
    public function showLatestTransactions($value) {
        return $this->purchaseInterface->getLatestTransactionsByValue($value);
    }

    /**
     * Get sales by manufacturer
     */
    public function salesByManufacturer(Request $request) {
        return $this->purchaseInterface->salesByManufacturer($request);
    }

    /**
     * Get sales by merchant
     */
    public function salesByMerchant(Request $request) {
        return $this->purchaseInterface->salesByMerchant($request);
    }

    /**
     * Get sales by product
     */
    public function salesByProduct(Request $request) {
        return $this->purchaseInterface->salesByProduct($request);
    }

    /**
     * Get sales by barangay
     */
    public function salesByBarangay(Request $request) {
        return $this->purchaseInterface->salesByBarangay($request);
    }

    /**
     * Get sales by city
     */
    public function salesByCity(Request $request) {
        return $this->purchaseInterface->salesByCity($request);
    }

    /**
     * Get sales by province
     */
    public function salesByProvince(Request $request) {
        return $this->purchaseInterface->salesByProvince($request);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request) {
        return $this->purchaseInterface->updateOrderStatus($request);
    }
}
