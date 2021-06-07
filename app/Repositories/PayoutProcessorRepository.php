<?php

namespace App\Repositories;

use App\PayoutProcessor;
use App\Interfaces\PayoutProcessorInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PayoutProcessorRepository implements PayoutProcessorInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAll()
    {
        try {
            $processors = PayoutProcessor::all();

            if ($processors->count() < 1) {
                return $this->error("Payout processors not found", 404);
            }

            return $this->success("All payout processors", $processors);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getById($id)
    {
        try {
            $processor = PayoutProcessor::find($id);

            if ($processor->count() < 1) {
                return $this->error("Payout processor not found", 404);
            }

            return $this->success("Payout processor detail", $processor);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        try {
            $processor = PayoutProcessor::find($id);

            if (!$processor) return $this->error("Payout processor not found", 404);

            $inputs = [
                'proc_id' => $request->proc_id,
                'description' => $request->proc_id
            ];
            $rules = [
                'proc_id' => 'required',
                'description' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $processor->update($inputs);

            return $this->success("Payout processor updated", $processor);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $processor = PayoutProcessor::find($id);

            if (!$processor) return $this->error("Payout processor not found", 404);

            $processor->delete();

            DB::commit();
            return $this->success("Payout processor deleted", $processor);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function create(Request $request)
    {
        try {
            $inputs = [
                'proc_id' => $request->proc_id,
                'description' => $request->description
            ];
            $rules = [
                'proc_id' => 'required',
                'description' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $processor = PayoutProcessor::create([
                'proc_id' => $request->proc_id,
                'description' => $request->description
            ]);

            return $this->success("Payout processor created", $processor);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
