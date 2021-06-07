<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseAPI;
use App\Interfaces\VoucherOrderInterface;
use App\Interfaces\NotificationInterface;

class VoucherOrderController extends Controller
{
    use ResponseAPI;

    protected $voucherOrderInterface;
    protected $notification;

    public function __construct(VoucherOrderInterface $voucherOrderInterface, NotificationInterface $notificationInterface)
    {
        $this->voucherOrderInterface = $voucherOrderInterface;
        $this->notification= $notificationInterface;
    }

    public function create(Request $request)
    {
        return $this->voucherOrderInterface->createVoucherOrder($request);
    }

    public function show($id)
    {
        return $this->voucherOrderInterface->getVoucherOrderById($id);
    }

    public function uploadProofOfPayment(Request $request)
    {
        //todo notify admin for merchans voucher order for the receip uploaded
        return $this->voucherOrderInterface->uploadProofOfPayment($request);

    }

    public function verify($id)
    {
        return $this->voucherOrderInterface->verifyVoucherOrder($id);

    }
    public function reject($id)
    {
        return $this->voucherOrderInterface->rejectVoucherOrder($id);
    }

    public function orders()
    {
        return $this->voucherOrderInterface->getVoucherOrders();
    }

    public function toVerify()
    {
        return $this->voucherOrderInterface->toVerify();
    }

    public function history()
    {
        return $this->voucherOrderInterface->history();
    }

    public function countPendingRequest()
    {
        return $this->voucherOrderInterface->countPendingRequest();
    }

    public function unpaid()
    {
        return $this->voucherOrderInterface->unpaid();
    }

    public function feesCollected()
    {
        return $this->voucherOrderInterface->feesCollected();
    }

    public function voucherSold()
    {
        return $this->voucherOrderInterface->voucherSold();
    }

    public function noProofOfPayment($id)
    {
        return $this->voucherOrderInterface->noProofOfPayment($id);
    }

    public function cancelOrder($id)
    {
        return $this->voucherOrderInterface->cancelOrder($id);
    }
}
