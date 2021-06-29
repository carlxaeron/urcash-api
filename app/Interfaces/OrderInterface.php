<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface OrderInterface
{
    public function updateOrderStatus(Request $request);

    public function getAllMerchantOrders(Request $request);
    
    public function getAllOrders(Request $request);
}