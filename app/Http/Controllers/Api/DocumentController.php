<?php

namespace App\Http\Controllers\Api;

use App\Document;
use App\Http\Controllers\Controller;
use App\Traits\ResponseAPI;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use ResponseAPI;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        DB::beginTransaction();
        try {
            if(request()->page) $docs = Document::with(['documentable'])->paginate(request()->per_page ?? 10);
            else $docs = Document::with(['documentable'])->get();

            return $this->success('Successfully get all the documents.', $docs);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $validation = Validator::make($request->all(), [
                'title'=>['required',
                function($attr,$value,$fail) use($user){
                    if($user->documents()->where('title',$value)->first()) $fail("Document type/title is already exists in this user.");
                }
                ],
                'file'=>['required','file',
                ],
            ]);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $inputs = $request->only(array_keys($validation->getRules()));

            $inputs['path'] = $inputs['file']->store('image/documents');
            unset($inputs['file']);

            $user->documents()->create($inputs);

            DB::commit();

            $user = User::find($user->id);

            return $this->success('Successfully saved the document to the user.', $user);
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
    public function showByUserId($id)
    {
        try {
            $document = User::find($id)->documents()->get();
            return $this->success('Successfully fetched all the documents of the user.', $document);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
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
        DB::beginTransaction();
        try {
            $doc = Document::where('id',$id);
            if(!Auth::user()->hasRole('administrator')) {
                $doc = Auth::user()->documents()->where('id',$id);
            }
            $doc = $doc->first();
            if($doc) {
                $doc->delete();
                DB::commit();
                return $this->success('Successfully deleted.', []);
            } 

            return $this->success('Deleted unsuccessful.', []);

        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage());
        }
    }
}
