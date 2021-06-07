<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PriceInterface;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    protected $priceInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(PriceInterface $priceInterface) {
        $this->priceInterface = $priceInterface;
    }

    /**
     * Get all prices
     */
    public function index() {
        return $this->priceInterface->getAllPrices();
    }

    /**
     * Get price by ID
     */
    public function show($id) {
        return $this->priceInterface->getPriceById($id);
    }

    /**
     * Update price
     */
    public function update(Request $request, $id) {
        return $this->priceInterface->updatePrice($request, $id);
    }

    /**
     * Delete price
     */
    public function delete($id) {
        return $this->priceInterface->deletePrice($id);
    }

    /**
     * Create price
     */
    public function create(Request $request) {
        return $this->priceInterface->createPrice($request);
    }
}
