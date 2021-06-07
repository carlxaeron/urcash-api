<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\SupportTicketInterface;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    protected $supportTicketInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(SupportTicketInterface $supportTicketInterface) {
        $this->supportTicketInterface = $supportTicketInterface;
    }

    /**
     * Get all support tickets
     */
    public function index() {
        return $this->supportTicketInterface->getAllSupportTickets();
    }

    /**
     * Get support ticket by ID
     */
    public function show($id) {
        return $this->supportTicketInterface->getSupportTicketById($id);
    }

    /**
     * Get support tickets by logged in user and by status
     */
    public function showByAuthUserAndStatus($status) {
        return $this->supportTicketInterface->getSupportTicketsByAuthUserAndStatus($status);
    }

    /**
     * Get all support tickets whose issue is Account Lock Request Review
     */
    public function showAllAccountLockRequestReview() {
        return $this->supportTicketInterface->getAllAccountLockRequestReviewSupportTickets();
    }

    /**
     * Get all support tickets assigned to a user whose role is customer-support
     */
    public function showByCustomerSupportUserId($user_id) {
        return $this->supportTicketInterface->getSupportTicketsAssignedToCustomerAgent($user_id);
    }

    /**
     * Get support ticket by reference_number
     */
    public function showSupportTicketByReferenceNumber($reference_number) {
        return $this->supportTicketInterface->getSupportTicketByReferenceNumber($reference_number);
    }

    /**
     * Get support tickets by mobile_number
     */
    public function showByMobileNumber($mobile_number) {
        return $this->supportTicketInterface->getSupportTicketsByMobileNumber($mobile_number);
    }

    /**
     * Get support tickets by email
     */
    public function showByEmail($email) {
        return $this->supportTicketInterface->getSupportTicketsByEmail($email);
    }

    /**
     * Get support tickets by issue
     */
    public function showByIssue($issue) {
        return $this->supportTicketInterface->getSupportTicketsByIssue($issue);
    }

    /**
     * Get support tickets by priority
     */
    public function showByPriority($priority) {
        return $this->supportTicketInterface->getSupportTicketsByPriority($priority);
    }

    /**
     * Get support tickets by is_resolved
     */
    public function showByIsResolved($status) {
        return $this->supportTicketInterface->getSupportTicketsByStatus($status);
    }

    /**
     * Compose message for replying to support ticket
     */
    public function compose(Request $request) {
        return $this->supportTicketInterface->respondToSupportTicket($request);
    }

    /**
     * Update support ticket and mark as resolved
     */
    public function update($id) {
        return $this->supportTicketInterface->updateSupportTicket($id);
    }

    /**
     * Delete support ticket
     */
    public function delete($id) {
        return $this->supportTicketInterface->deleteSupportTicket($id);
    }

    /**
     * Create support ticket
     */
    public function create(Request $request) {
        return $this->supportTicketInterface->createSupportTicket($request);
    }
}
