<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PurchaseInterface
{
    /**
     * Get all purchase transactions
     *
     * @method  GET api/transactions
     * @access  public
     */
    public function getAllTransactions();

    /**
     * Get purchase by ID
     *
     * @param   integer $id
     * @method  GET api/transactions/{id}
     * @access  public
     */
    public function getTransactionById($id);

    /**
     * Get purchase transactions of a shop by ID
     *
     * @param   integer $id
     * @method  GET api/transactions/shop/{id}
     * @access  public
     */
    public function getTransactionsByShop($id);

    /**
     * Get all purchase transactions for current day
     *
     * @method  GET api/transactions/today
     * @access  public
     */
    public function getTransactionsToday();

    /**
     * Get top 5 cities of products for purchase transactions
     *
     * @method  GET api/transactions/top5/cities
     * @access  public
     */
    public function getTransactionsTop5Cities();

    /**
     * Get top 5 manufacturers of products for purchase transactions
     *
     * @method  GET api/transactions/top5/manufacturers
     * @access  public
     */
    public function getTransactionsTop5Manufacturers();

    /**
     * Get top 5 merchants of products for purchase transactions
     *
     * @method  GET api/transactions/top5/merchants
     * @access  public
     */
    public function getTransactionsTop5Merchants();

    /**
     * Get top 5 products for purchase transactions
     *
     * @method  GET api/transactions/top5/products
     * @access  public
     */
    public function getTransactionsTop5Products();

    /**
     * Get latest purchase transactions by value
     *
     * @param  integer $value
     * @method  GET api/transactions/latest/{$value}
     * @access  public
     */
    public function getLatestTransactionsByValue($value);

    /**
     * Get sales by manufacturer
     *
     * @param   Request $request
     * @method  POST    api/sales/manufacturers
     * @access  public
     */
    public function salesByManufacturer(Request $request);

    /**
     * Get sales by merchant
     *
     * @param   Request $request
     * @method  POST    api/sales/merchants
     * @access  public
     */
    public function salesByMerchant(Request $request);

    /**
     * Get sales by product
     *
     * @param   Request $request
     * @method  POST    api/sales/products
     * @access  public
     */
    public function salesByProduct(Request $request);

    /**
     * Get sales by barangay
     *
     * @param   Request $request
     * @method  POST    api/sales/barangay
     * @access  public
     */
    public function salesByBarangay(Request $request);

    /**
     * Get sales by city
     *
     * @param   Request $request
     * @method  POST    api/sales/city
     * @access  public
     */
    public function salesByCity(Request $request);

    /**
     * Get sales by province
     *
     * @param   Request $request
     * @method  POST    api/sales/province
     * @access  public
     */
    public function salesByProvince(Request $request);

    // public function updateOrderStatus(Request $request);
}
