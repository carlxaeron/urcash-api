<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface VerificationRequestInterface
{
    /**
     * Get all verification requests
     *
     * @method  GET api/verification_requests
     * @access  public
     */
    public function getAllVerificationRequests();

    /**
     * Get verification request by ID
     *
     * @param   integer $id
     * @method  GET api/verification_requests/{id}
     * @access  public
     */
    public function getVerificationRequestById($id);

    /**
     * Get verification requests by logged in user and by status
     *
     * @param   string $status
     * @method  GET api/verification_requests/user/status/{status}
     * @access  public
     */
    public function getVerificationRequestsByAuthUserAndStatus($status);

    /**
     * Get verification requests by user ID
     *
     * @param   integer $id
     * @method  GET api/verification_requests/user/{id}
     * @access  public
     */
    public function getVerificationRequestsByUserId($user_id);

    /**
     * Get verification requests by type
     *
     * @param   string $type
     * @method  GET api/verification_requests/type/{type}
     * @access  public
     */
    public function getVerificationRequestsByType($type);

    /**
     * Get verification requests by document whose type is merchant_verification
     *
     * @param   string $document
     * @method  GET api/verification_requests/document/{document}
     * @access  public
     */
    public function getVerificationRequestsByDocument($document);

    /**
     * Get verification requests by status
     *
     * @param   string $status
     * @method  GET api/verification_requests/status/{status}
     * @access  public
     */
    public function getVerificationRequestsByStatus($status);

    /**
     * Get verification request file path of uploaded document by ID
     *
     * @param   integer $id
     * @method  GET api/verification_requests/{id}/get_upload_path
     * @access  public
     */
    public function getImagePath($id);

    /**
     * Update verification request
     *
     * @param   Request $request, integer $id
     * @method  POST    api/verification_requests/{id}/update
     * @access  public
     */
    public function updateVerificationRequest(Request $request, $id);

    /**
     * Delete verification request
     *
     * @param   integer $id
     * @method  DELETE  api/verification_requests/{id}/delete
     * @access  public
     */
    public function deleteVerificationRequest($id);

    /**
     * Create verification request
     *
     * @param   Request $request
     * @method  POST    api/verification_requests/create
     * @access  public
     */
    public function createVerificationRequest(Request $request);
}
