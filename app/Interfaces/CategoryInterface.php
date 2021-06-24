<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface CategoryInterface {
    /**
     * Create Category
     *
     * @method  GET api/v1/category
     * @access  public
     */
    public function listCategory();
    
    /**
     * Create Category
     *
     * @method  POST api/v1/category
     * @access  public
     */
    public function createCategory(Request $request);

    /**
     * Update Category
     *
     * @method  PUT api/v1/category/{id}
     * @access  public
     */
    public function updateCategory(Request $request, $category);

    /**
     * Delete Category
     *
     * @method  DELETE api/v1/category/{id}
     * @access  public
     */
    public function deleteCategory(Request $request, $category);
}