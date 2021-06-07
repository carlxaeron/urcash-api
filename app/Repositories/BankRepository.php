<?php

namespace App\Repositories;

use App\Bank;
use App\Interfaces\BankInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class BankRepository implements BankInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllBanks()
    {
        try {
            $banks = Bank::all();

            if ($banks->count() < 1) {
                return $this->error("Banks not found", 404);
            }

            return $this->success("All banks", $banks);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getBankById($id)
    {
        try {
            $bank = Bank::find($id);

            if (!$bank) return $this->error("Bank not found", 404);

            return $this->success("Bank detail", $bank);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchBanks(Request $request)
    {
        try {
            $search_query = $request->search_query;
            $banks = $this->getAllBanks();

            if ($banks->getData()->statusCode == 404) {
                return $this->error($banks->getData()->message);
            } elseif ($search_query == '' || $search_query == null) { // Return all records if search query is null
                return $this->success($banks->getData()->message, array(
                    "banks" => $banks->getData()->results,
                    "count" => count($banks->getData()->results)
                ));
            }

            $filter_banks = Bank::where('name', 'like', '%' .$search_query. '%')->get();
            $results_count = $filter_banks->count();

            if ($results_count < 1) {
                return $this->error("No results returned from your query", 404);
            } elseif ($results_count == 1) {
                $message = "Search returned 1 result";
            } else {
                $message = "Search returned $results_count results";
            }
            return $this->success($message, array(
                "banks" => $filter_banks,
                "count" => $results_count
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
