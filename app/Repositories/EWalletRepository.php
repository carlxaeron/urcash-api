<?php

namespace App\Repositories;

use App\EWallet;
use App\Interfaces\EWalletInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class EWalletRepository implements EWalletInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllEWallets()
    {
        try {
            $e_wallet = EWallet::all();

            if ($e_wallet->count() < 1) {
                return $this->error("e-Wallets not found", 404);
            }

            return $this->success("All e-Wallets", $e_wallet);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getEWalletById($id)
    {
        try {
            $e_wallet = EWallet::find($id);

            if (!$e_wallet) return $this->error("e-Wallet not found", 404);

            return $this->success("e-Wallet detail", $e_wallet);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchEWallets(Request $request)
    {
        try {
            $search_query = $request->search_query;
            $e_wallets = $this->getAllEWallets();

            if ($e_wallets->getData()->statusCode == 404) {
                return $this->error($e_wallets->getData()->message);
            } elseif ($search_query == '' || $search_query == null) { // Return all records if search query is null
                return $this->success($e_wallets->getData()->message, array(
                    "e_wallets" => $e_wallets->getData()->results,
                    "count" => count($e_wallets->getData()->results)
                ));
            }

            $filter_e_wallets = EWallet::where('name', 'like', '%' .$search_query. '%')->get();
            $results_count = $filter_e_wallets->count();

            if ($results_count < 1) {
                return $this->error("No results returned from your query", 404);
            } elseif ($results_count == 1) {
                $message = "Search returned 1 result";
            } else {
                $message = "Search returned $results_count results";
            }
            return $this->success($message, array(
                "e_wallets" => $filter_e_wallets,
                "count" => $results_count
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
