<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface AddressInterface
{
    /**
     * Get all addresses
     *
     * @method  GET api/addresses
     * @access  public
     */
    public function getAllAddresses();

    /**
     * Get address by ID
     *
     * @param   integer $id
     * @method  GET api/addresses/{id}
     * @access  public
     */
    public function getAddressById($id);

    /**
     * Get all addresses that are business addresses of shops
     *
     * @method  GET api/addresses/shops
     * @access  public
     */
    public function getAddressesByCompleteAddress();

    /**
     * Get addresses by barangay
     *
     * @method  GET api/addresses/barangay
     * @access  public
     */
    public function getAddressesByBarangay();

    /**
     * Get addresses by city
     *
     * @method  GET api/addresses/city
     * @access  public
     */
    public function getAddressesByCity();

    /**
     * Get addresses by province
     *
     * @method  GET api/addresses/province
     * @access  public
     */
    public function getAddressesByProvince();
}
