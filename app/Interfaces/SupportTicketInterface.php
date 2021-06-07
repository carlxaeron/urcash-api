<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface SupportTicketInterface
{
    /**
     * Get all support tickets
     *
     * @method  GET api/support_tickets
     * @access  public
     */
    public function getAllSupportTickets();

    /**
     * Get support ticket by ID
     *
     * @param   integer $id
     * @method  GET api/support_tickets/{id}
     * @access  public
     */
    public function getSupportTicketById($id);

    /**
     * Get support tickets by logged in user and by status
     *
     * @param   string $status
     * @method  GET api/support_tickets/user/status/{status}
     * @access  public
     */
    public function getSupportTicketsByAuthUserAndStatus($status);

    /**
     * Get all support tickets whose issue is Account Lock Request Review
     *
     * @method  GET api/support_tickets/type/account_lock/
     * @access  public
     */
    public function getAllAccountLockRequestReviewSupportTickets();

    /**
     * Get all support tickets assigned to a user whose role is customer-support
     *
     * @param   integer $id
     * @method  GET api/support_tickets/user/{$id}
     * @access  public
     */
    public function getSupportTicketsAssignedToCustomerAgent($user_id);

    /**
     * Get support ticket by reference_number
     *
     * @param   integer $reference_number
     * @method  GET api/support_tickets/reference_number/{reference_number}
     * @access  public
     */
    public function getSupportTicketByReferenceNumber($reference_number);

    /**
     * Get support tickets by mobile_number
     *
     * @param   integer $mobile_number
     * @method  GET api/support_tickets/mobile_number/{mobile_number}
     * @access  public
     */
    public function getSupportTicketsByMobileNumber($mobile_number);

    /**
     * Get support tickets by email
     *
     * @param   string $email
     * @method  GET api/support_tickets/email/{email}
     * @access  public
     */
    public function getSupportTicketsByEmail($email);

    /**
     * Get support tickets by issue
     *
     * @param   string $issue
     * @method  GET api/support_tickets/issue/{issue}
     * @access  public
     */
    public function getSupportTicketsByIssue($issue);

    /**
     * Get support tickets by priority
     *
     * @param   string $priority
     * @method  GET api/support_tickets/priority/{priority}
     * @access  public
     */
    public function getSupportTicketsByPriority($priority);

    /**
     * Get support tickets by is_resolved
     *
     * @param   string $status
     * @method  GET api/support_tickets/status/{status}
     * @access  public
     */
    public function getSupportTicketsByStatus($status);

    /**
     * Compose message for replying to support ticket
     *
     * @param   Request $request
     * @method  POST    api/support_tickets/compose
     * @access  public
     */
    public function respondToSupportTicket(Request $request);

    /**
     * Update support ticket and mark as resolved
     *
     * @param   integer $id
     * @method  POST    api/support_tickets/{id}/update
     * @access  public
     */
    public function updateSupportTicket($id);

    /**
     * Delete support ticket
     *
     * @param   integer $id
     * @method  DELETE  api/support_tickets/{id}/delete
     * @access  public
     */
    public function deleteSupportTicket($id);

    /**
     * Create support ticket
     *
     * @param   Request $request
     * @method  POST    api/support_tickets/create
     * @access  public
     */
    public function createSupportTicket(Request $request);
}
