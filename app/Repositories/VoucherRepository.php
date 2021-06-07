<?php

namespace App\Repositories;

use App\Voucher;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use App\Interfaces\VoucherInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherRepository implements VoucherInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllVouchers()
    {
        try {
            $voucher = Voucher::all()->where('status', '=', true);

            if ($voucher->count() < 1) {
                return $this->error("Voucher not found", 404);
            }

            return $this->success("Voucher", $voucher);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVoucherById($id)
    {
        try {
            $voucher = Voucher::find($id);

            if (!$voucher) return $this->error("Voucher not found", 404);

            return $this->success("Voucher", $voucher);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createVoucher(Request $request)
    {
        try {
            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 1
            ];
            $rules = [
                'title' => 'required',
                'description' => 'required',
                'amount' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Initialize GenerateRandomIntegers
            $qrCode = new GenerateRandomIntegers(1, 9, 6);

            $voucher = Voucher::create([
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 1
            ]);

            return $this->success("Voucher created", $voucher);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateVoucher(Request $request, $id)
    {
        try {
            $voucher = Voucher::find($id);

            if (!$voucher) return $this->error("Voucher not found", 404);

            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => $request->status
            ];
            $rules = [
                'title' => 'required',
                'description' => 'required',
                'amount' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $voucher->title = $request->title;
            $voucher->description = $request->description;
            $voucher->amount = $request->amount;
            $voucher->update();

            return $this->success("Voucher updated", $voucher);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteVoucher($id)
    {
        DB::beginTransaction();
        try {
            $voucher = Voucher::find($id);

            // Check the user
            if (!$voucher) return $this->error("Voucher not found", 404);

            $voucher->status = false;
            // Delete the user
            $voucher->update();

            DB::commit();
            return $this->success("Voucher deleted", $voucher);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
