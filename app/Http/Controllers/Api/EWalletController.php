<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\EWalletInterface;
use Illuminate\Http\Request;

class EWalletController extends Controller
{
    protected $eWalletInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(EWalletInterface $eWalletInterface) {
        $this->eWalletInterface = $eWalletInterface;
    }

    /**
     * Get all e-wallets
     */
    public function index() {
        return $this->eWalletInterface->getAllEWallets();
    }

    /**
     * Get e-wallet by ID
     */
    public function show($id) {
        return $this->eWalletInterface->getEWalletById($id);
    }

    /**
     * Search e-wallets based on query
     */
    public function searchEWallets(Request $request) {
        return $this->eWalletInterface->searchEWallets($request);
    }
}
