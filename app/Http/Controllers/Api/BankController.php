<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\BankInterface;
use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $bankInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(BankInterface $bankInterface) {
        $this->bankInterface = $bankInterface;
    }

    /**
     * Get all banks
     */
    public function index() {
        return $this->bankInterface->getAllBanks();
    }

    /**
     * Get bank by ID
     */
    public function show($id) {
        return $this->bankInterface->getBankById($id);
    }

    /**
     * Search banks based on query
     */
    public function searchBanks(Request $request) {
        return $this->bankInterface->searchBanks($request);
    }
}
