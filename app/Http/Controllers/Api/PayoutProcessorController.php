<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\PayoutProcessorInterface;

class PayoutProcessorController extends Controller
{
    protected $payoutProcessorInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(PayoutProcessorInterface $payoutProcessorInterface)
    {
        $this->payoutProcessorInterface = $payoutProcessorInterface;
    }

    /**
     * Get all Payout processor.
     */
    public function index()
    {
        return $this->payoutProcessorInterface->getAll();
    }

    /**
     * Get Payout processor by ID.
     */
    public function show($id)
    {
        return $this->payoutProcessorInterface->getById($id);
    }

    /**
     * Update a Payout processor.
     */
    public function update(Request $request, $id)
    {
        return $this->payoutProcessorInterface->update($request, $id);
    }

    /**
     * Delete a Payout processor.
     */
    public function delete($id)
    {
        return $this->payoutProcessorInterface->delete($id);
    }

    /**
     * Create a Payout processor.
     */
    public function create(Request $request)
    {
        return $this->payoutProcessorInterface->create($request);
    }
}
