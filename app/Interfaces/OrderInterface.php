<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface OrderInterface
{
    public function updateOrderStatus(Request $request);

    public function getAllUserOrders(Request $request);
    
    public function getAllOrders(Request $request);
}