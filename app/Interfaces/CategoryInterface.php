<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface CategoryInterface {
    /**
     * Create Category
     *
     * @method  POST api/v1/category
     * @access  public
     */
    public function createCategory(Request $request);
}