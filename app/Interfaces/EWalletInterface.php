<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EWalletInterface
{
    /**
     * Get all e-wallets
     *
     * @method  GET api/e-wallets
     * @access  public
     */
    public function getAllEWallets();

    /**
     * Get e-wallet by ID
     *
     * @param   integer $id
     * @method  GET api/e-wallets/{id}
     * @access  public
     */
    public function getEWalletById($id);

    /**
     * Search e-wallets based on query
     *
     * @param   Request $request
     * @method  GET api/e-wallets/search
     * @access  public
     */
    public function searchEWallets(Request $request);
}
