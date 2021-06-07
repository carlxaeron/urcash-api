<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PriceInterface
{
    /**
     * Get all prices
     *
     * @method  GET api/prices
     * @access  public
     */
    public function getAllPrices();

    /**
     * Get price by ID
     *
     * @param   integer $id
     * @method  GET api/prices/{id}
     * @access  public
     */
    public function getPriceById($id);

    /**
     * Update price
     *
     * @param   Request $request
     * @param   integer $id
     * @method  POST    api/prices/{id}/update
     * @access  public
     */
    public function updatePrice(Request $request, $id);

    /**
     * Delete price
     *
     * @param   integer $id
     * @method  DELETE  api/prices/{id}/delete
     * @access  public
     */
    public function deletePrice($id);

    /**
     * Create price
     *
     * @param   Request $request
     * @method  POST    api/prices/create
     * @access  public
     */
    public function createPrice(Request $request);
}
