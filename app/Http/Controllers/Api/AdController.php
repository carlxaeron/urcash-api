<?php

namespace App\Http\Controllers\Api;

use App\Ad;
use App\Http\Controllers\Controller;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    use ResponseAPI;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $name)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'name'=>$name,
                'images'=>$request->images
            ];

            $rules = [
                'name'=>'required|alpha',
                'images'=>'required|array',
                'images.*'=>'image'
            ];

            $validation = Validator::make($inputs, $rules);

            if($validation->fails()) return $this->error($validation->errors());

            $ad = Ad::where('name',$name)->first();

            if(!$ad) {
                $ad = Ad::create($inputs);
            }

            foreach($request->images as $img) {
                $ad->images()->create(['path'=>$img->store('ad')]);
                $ad->save();
            }

            $ad = Ad::where('name',$name)->first();

            DB::commit();

            return $this->success('Success', $ad);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
