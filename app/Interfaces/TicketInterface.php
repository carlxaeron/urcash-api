<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TicketInterface
{
     /**
     * Get all ticket
     *
     * @access  public
     */
    public function getAllTickets();

    /**
     * Get ticket by ID
     *
     * @access  public
     */
    public function getTicketById($id);

     /**
     * Create ticket
     *
     * @access  public
     */
    public function createTicket(Request $request);

     /**
     * Update ticket
     *
     * @access  public
     */
    public function updateTicket(Request $request, $id);

     /**
     * delete ticket
     *
     * @access  public
     */
    public function deleteTicket($id);

     /**
     * Get ticket by QR code
     *
     * @access  public
     */
    public function getTicketByQRCode($qr_code);
}
