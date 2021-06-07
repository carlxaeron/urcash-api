<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PayoutProcessorInterface
{
     /**
     * Get all payout processors
     *
     * @access  public
     */
    public function getAll();

     /**
     * Get payout processor by ID
     *
     * @param   integer $id
     * @access  public
     */
    public function getById($id);

     /**
     * Update payout processor
     *
     * @access  public
     */
    public function update($request, $id);

     /**
     * Delete payout processor
     *
     * @access  public
     */
    public function delete($id);

     /**
     * Create payout processor
     *
     * @access  public
     */
    public function create(Request $request);
}
