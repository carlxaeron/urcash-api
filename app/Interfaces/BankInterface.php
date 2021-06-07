<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface BankInterface
{
    /**
     * Get all banks
     *
     * @method  GET api/banks
     * @access  public
     */
    public function getAllBanks();

    /**
     * Get bank by ID
     *
     * @param   integer $id
     * @method  GET api/banks/{id}
     * @access  public
     */
    public function getBankById($id);

    /**
     * Search banks based on query
     *
     * @param   Request $request
     * @method  GET api/banks/search
     * @access  public
     */
    public function searchBanks(Request $request);
}
