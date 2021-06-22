<?php
namespace App\Repositories;

use App\Category;
use App\Interfaces\CategoryInterface;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryRepository implements CategoryInterface {
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function createCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'name' => $request->name,
            ];
            $rules = [
                'name' => ['required',function($attr,$value,$fail) {
                    if(Category::where('name', strtoupper($value))->count()) $fail("The category is already exists!");
                }],
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $create = Category::create([
                'name'=>$request->name,
            ]);

            DB::commit();

            return $this->success('Successfully created.', $create);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    public function updateCategory(Request $request, $category)
    {
        DB::beginTransaction();

        $category = $category->find($request->id);
        if(!$category) return $this->error('Category not found.');

        if($category->name == strtoupper($request->name)) return $this->error('No update.');

        try {
            $inputs = [
                'name' => $request->name,
            ];
            $rules = [
                'name' => ['required',function($attr,$value,$fail) use($category, $request) {
                    if( $category->name != strtoupper($request->name) 
                    && Category::where('name', strtoupper($value))->count()) $fail("The category is already exists!");
                }
                ],
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $create = $category->update($inputs);

            DB::commit();

            return $this->success('Successfully updated.', $create, 204);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    public function deleteCategory(Request $request, $category) {
        DB::beginTransaction();

        $category = $category->find($request->id);
        if(!$category) return $this->error('Category not found.');

        try {
            $category->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}