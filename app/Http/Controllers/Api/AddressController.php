<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\AddressInterface;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(AddressInterface $addressInterface) {
        $this->addressInterface = $addressInterface;
    }

    /**
     * Get all addresses
     */
    public function index() {
        return $this->addressInterface->getAllAddresses();
    }

    /**
     * Get address by ID
     */
    public function show($id) {
        return $this->addressInterface->getAddressById($id);
    }

    /**
     * Get all addresses that are business addresses of shops
     */
    public function showByShopAddress() {
        return $this->addressInterface->getAddressesByCompleteAddress();
    }

    /**
     * Get addresses by barangay
     */
    public function showByBarangay() {
        return $this->addressInterface->getAddressesByBarangay();
    }

    /**
     * Get addresses by city
     */
    public function showByCity() {
        return $this->addressInterface->getAddressesByCity();
    }

    /**
     * Get addresses by province
     */
    public function showByProvince() {
        return $this->addressInterface->getAddressesByProvince();
    }
}
