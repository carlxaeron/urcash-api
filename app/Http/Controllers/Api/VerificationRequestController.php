<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\VerificationRequestInterface;
use Illuminate\Http\Request;

class VerificationRequestController extends Controller
{
    protected $verificationRequestInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(VerificationRequestInterface $verificationRequestInterface) {
        $this->verificationRequestInterface = $verificationRequestInterface;
    }

    /**
     * Get all verification requests
     */
    public function index() {
        return $this->verificationRequestInterface->getAllVerificationRequests();
    }

    /**
     * Get verification request by ID
     */
    public function show($id) {
        return $this->verificationRequestInterface->getVerificationRequestById($id);
    }

    /**
     * Get verification requests by logged in user and by status
     */
    public function showByAuthUserAndStatus($status) {
        return $this->verificationRequestInterface->getVerificationRequestsByAuthUserAndStatus($status);
    }

    /**
     * Get verification requests by user ID
     */
    public function showByUserId($user_id) {
        return $this->verificationRequestInterface->getVerificationRequestsByUserId($user_id);
    }

    /**
     * Get verification requests by type
     */
    public function showByType($type) {
        return $this->verificationRequestInterface->getVerificationRequestsByType($type);
    }

    /**
     * Get verification requests by document whose type is merchant_verification
     */
    public function showByDocument($document) {
        return $this->verificationRequestInterface->getVerificationRequestsByDocument($document);
    }

    /**
     * Get verification requests by status
     */
    public function showByStatus($status) {
        return $this->verificationRequestInterface->getVerificationRequestsByStatus($status);
    }

    /**
     * Get verification request file path of uploaded document by ID
     */
    public function showImagePath($id) {
        return $this->verificationRequestInterface->getImagePath($id);
    }

    /**
     * Update verification request
     */
    public function update(Request $request, $id) {
        return $this->verificationRequestInterface->updateVerificationRequest($request, $id);
    }

    /**
     * Delete verification request
     */
    public function delete($id) {
        return $this->verificationRequestInterface->deleteVerificationRequest($id);
    }

    /**
     * Create verification request
     */
    public function create(Request $request) {
        return $this->verificationRequestInterface->createVerificationRequest($request);
    }
}
