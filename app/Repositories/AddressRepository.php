<?php

namespace App\Repositories;

use App\Address;
use App\Interfaces\AddressInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressRepository implements AddressInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllAddresses()
    {
        try {
            $addresses = Address::all();

            if ($addresses->count() < 1) {
                return $this->error("Addresses not found", 404);
            }

            return $this->success("All addresses", $addresses);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAddressById($id)
    {
        try {
            $address = Address::find($id);

            if (!$address) return $this->error("Address not found", 404);

            return $this->success("Address detail", $address);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAddressesByCompleteAddress()
    {
        try {
            $addresses = Address::whereNotNull('complete_address')->get(); // Get all shop addresses

            if ($addresses->count() < 1) {
                return $this->error("Shop addresses not found", 404);
            }

            return $this->success("All shop addresses", $addresses);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAddressesByBarangay()
    {
        try {
            $addresses = Address::select('barangay')->distinct()->get();
            $barangays = array();

            foreach ($addresses as $address) array_push($barangays, $address->barangay);
            sort($barangays,SORT_STRING | SORT_FLAG_CASE);

            return $this->success("All barangays", $barangays);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAddressesByCity()
    {
        try {
            $addresses = Address::select('city')->distinct()->get();
            $cities = array();

            foreach ($addresses as $address) array_push($cities, $address->city);
            sort($cities,SORT_STRING | SORT_FLAG_CASE);

            return $this->success("All cities", $cities);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAddressesByProvince()
    {
        try {
            $addresses = Address::select('province')->distinct()->get();
            $provinces = array();

            foreach ($addresses as $address) array_push($provinces, $address->province);
            sort($provinces,SORT_STRING | SORT_FLAG_CASE);

            return $this->success("All provinces", $provinces);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
