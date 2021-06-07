<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TicketPurchaseInterface
{
     /**
     * Get all ticket
     *
     * @access  public
     */
    public function getAllPurchaseTickets();

    /**
     * Get ticket by ID
     *
     * @access  public
     */
    public function getPurchaseTicketById($id);

     /**
     * Create ticket
     *
     * @access  public
     */
    public function createPurchaseTicket(Request $request);

     /**
     * Update ticket
     *
     * @access  public
     */
    public function updatePurchaseTicket(Request $request, $id);

     /**
     * delete ticket
     *
     * @access  public
     */
    public function deletePurchaseTicket($id);
}
