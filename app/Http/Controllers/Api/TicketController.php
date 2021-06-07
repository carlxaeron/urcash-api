<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\TicketInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(TicketInterface $ticketInterface)
    {
        $this->ticketInterface = $ticketInterface;
    }

    /**
     * Get all ticket.
     */
    public function index()
    {
        return $this->ticketInterface->getAllTickets();
    }

    /**
     * Get ticket by ID.
     */
    public function show($id)
    {
        return $this->ticketInterface->getTicketById($id);
    }

    /**
     * Update a ticket.
     */
    public function update(Request $request, $id)
    {
        return $this->ticketInterface->updateTicket($request, $id);
    }

    /**
     * Delete a ticket.
     */
    public function delete($id)
    {
        return $this->ticketInterface->deleteTicket($id);
    }

    /**
     * Create a ticket.
     */
    public function create(Request $request)
    {
        return $this->ticketInterface->createTicket($request);
    }

    /**
     * Get ticket by qr-code.
     */
    public function getTicketByQRCode(Request $request)
    {
        return $this->ticketInterface->getTicketByQRCode($request->qr_code);
    }
}
