<?php

namespace App\Repositories;

use App\Faq;
use App\Interfaces\FaqInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FaqRepository implements FaqInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllFaqs()
    {
        try {
            $faqs = Faq::all();

            if ($faqs->count() < 1) {
                return $this->error("Faqs not found", 404);
            }

            return $this->success("All faqs", $faqs);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getFaqById($id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) return $this->error("Faq not found", 404);

            return $this->success("Faq detail", $faq);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getFaqsByCategory($category)
    {
        try {
            $category_name = Faq::where('category', '=', $category)->first();

            if (!$category_name) return $this->error("Faq category not found", 404);

            $get_all_faqs_from_category = Faq::where('category', '=', $category)->get();

            return $this->success("Faqs in category $category", $get_all_faqs_from_category);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function searchFaqs(Request $request)
    {
        try {
            $faqs = Faq::all();

            $search_query = $request->search_query;

            if ($search_query == '' || $search_query == null) { // Return all records if search query is null
                return $this->success("All faqs", array(
                    "faqs" => $faqs,
                    "count" => $faqs->count()
                ));
            }

            $filter_faqs = Faq::where('category', 'like', '%' . $search_query . '%')
                ->orWhere('question', 'like', '%' . $search_query . '%')
                ->orWhere('answer', 'like', '%' . $search_query . '%')->get();

            $results_count = $filter_faqs->count();

            if ($results_count < 1) {
                return $this->success("No results returned from your query", array("count" => $results_count));
            }

            return $this->success("Search results", array(
                "faqs" => $filter_faqs,
                "count" => $results_count
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateFaq(Request $request, $id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) return $this->error("Faq not found", 404);

            $inputs = [
                'category' => $request->category,
                'question' => $request->question,
                'answer' => $request->answer
            ];
            $rules = [
                'category' => 'nullable',
                'question' => 'nullable',
                'answer' => 'nullable'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->has('category')) {
                $faq->category = $request->category;
            }
            if ($request->has('question')) {
                $faq->question = $request->question;
            }
            if ($request->has('answer')) {
                $faq->answer = $request->answer;
            }
            $faq->save();

            return $this->success("Faq updated", $faq);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteFaq($id)
    {
        DB::beginTransaction();
        try {
            $faq = Faq::find($id);

            if (!$faq) return $this->error("Faq not found", 404);

            $faq->delete();

            DB::commit();
            return $this->success("Faq deleted", $faq);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createFaq(Request $request)
    {
        try {
            $inputs = [
                'category' => $request->category,
                'question' => $request->question,
                'answer' => $request->answer
            ];
            $rules = [
                'category' => 'nullable',
                'question' => 'required',
                'answer' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if (!$request->category || $request->category == null) { // Set default category if field is null
                $request->category = "Uncategorized";
            }

            $faq = Faq::create([
                'category' => $request->category,
                'question' => $request->question,
                'answer' => $request->answer
            ]);

            return $this->success("Faq created", $faq);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
