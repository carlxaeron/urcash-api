<?php

namespace App\Repositories;

use App\Price;
use App\Product;
use App\Shop;
use App\User;
use App\VerificationRequest;
use App\Http\Helper\Utils\UploadImage;
use App\Interfaces\VerificationRequestInterface;
use App\Mail\VerificationRequestStatus;
use App\Repositories\AdminLogRepository;
use App\Repositories\PriceRepository;
use App\Repositories\ProductRepository;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VerificationRequestRepository implements VerificationRequestInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $type_choices = ['merchant_verification', 'product_verification', 'wallet_verification'];
    private $document_choices = ['bir', 'dti', 'mayor'];
    private $status_choices = ['resolved', 'unresolved'];

    public function getAllVerificationRequests()
    {
        try {
            $verification_requests = VerificationRequest::all();

            if ($verification_requests->count() < 1) {
                return $this->error("Verification requests not found", 404);
            }

            return $this->success("All verification requests", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestById($id)
    {
        try {
            $verification_request = VerificationRequest::find($id);

            if (!$verification_request) return $this->error("Verification request not found", 404);

            return $this->success("Verification request detail", $verification_request);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestsByAuthUserAndStatus($status)
    {
        try {
            $user = Auth::user();

            if (!$user) return $this->error("You are not authenticated", 401);

            $verification_requests_user = VerificationRequest::where('user_id', '=', $user->id)->get();

            if ($verification_requests_user->count() < 1) {
                return $this->error("Verification requests not found for user $user->id", 404);
            }

            $inputs = ['status' => $status];
            $rules = ['status' => ['required', Rule::in($this->status_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $status = Str::title($status); // transform as Resolved / Unresolved
            $verification_requests = VerificationRequest::where('user_id', '=', $user->id)
                ->where('status', '=', $status)->get();
            $requests_count = $verification_requests->count();

            if ($requests_count < 1 and $status == 'Resolved') {
                return $this->error("No verification requests have been accepted yet", 404);
            } elseif ($requests_count < 1 and $status == 'Unresolved') {
                return $this->error("No verification requests are currently open", 404);
            }

            return $this->success("Found $requests_count $status verification request(s) for user $user->id", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestsByUserId($user_id)
    {
        try {
            $inputs = ['user_id' => $user_id];
            $rules = ['user_id' => 'required|integer|exists:users,id'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $verification_requests = VerificationRequest::where('user_id', '=', $user_id)->get();
            $requests_count = $verification_requests->count();

            if ($requests_count < 1) {
                return $this->error("No verification requests are found for user $user_id", 404);
            }

            return $this->success("Found $requests_count verification request(s)", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestsByType($type)
    {
        try {
            $inputs = ['type' => $type];
            $rules = ['type' => ['required', Rule::in($this->type_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $verification_requests = VerificationRequest::where('type', '=', $type)->get();
            $requests_count = $verification_requests->count();

            if ($requests_count < 1) {
                return $this->error("No verification requests are found for type $type", 404);
            }

            return $this->success("Found $requests_count verification request(s)", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestsByDocument($document)
    {
        try {
            $inputs = ['document' => $document];
            $rules = ['document' => ['required', Rule::in($this->document_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($document == 'bir') {
                $doc = 'BIR Registration Certificate';
            } elseif ($document == 'dti') {
                $doc = 'DTI Permit';
            } else if ($document == 'mayor') {
                $doc = 'Mayor\'s Permit';
            }

            $verification_requests = VerificationRequest::where('type', '=', 'merchant_verification')
                ->where('document', '=', $doc)->get();
            $requests_count = $verification_requests->count();

            if ($verification_requests->count() < 1) {
                return $this->error("No merchant verification requests are found with document $doc", 404);
            }

            return $this->success("Found $requests_count verification request(s)", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVerificationRequestsByStatus($status)
    {
        try {
            $inputs = ['status' => $status];
            $rules = ['status' => ['required', Rule::in($this->status_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $status = Str::title($status); // transform as Resolved / Unresolved
            $verification_requests = VerificationRequest::where('status', '=', $status)->get();
            $requests_count = $verification_requests->count();

            if ($requests_count < 1 and $status == 'Resolved') {
                return $this->error("No verification requests have been accepted yet", 404);
            } elseif ($requests_count < 1 and $status == 'Unresolved') {
                return $this->error("No verification requests are currently open", 404);
            }

            return $this->success("Found $requests_count $status verification request(s)", $verification_requests);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getImagePath($id)
    {
        try {
            $verification_request = VerificationRequest::find($id);

            if (!$verification_request) return $this->error("Verification request not found", 404);

            if ($verification_request->type == 'product_verification') {
                return $this->error("This verification request does not have an image path because it is a product verification.");
            }

            $file_path = str_replace("\\\\", "\\", public_path($verification_request->uploaded_file_path));

            return $this->success("Verification request file path for id $id", $file_path);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateVerificationRequest(Request $request, $id)
    {
        try {
            $verification_request = VerificationRequest::find($id);

            if (!$verification_request) {
                return $this->error("Verification request not found", 404);
            } elseif ($verification_request->status == 'Resolved') {
                return $this->error("Verification request was previously resolved");
            }

            $inputs = ['is_accepted' => $request->is_accepted];
            $rules = ['is_accepted' => 'required|boolean'];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->is_accepted == 0) {
                $status_message = 'rejected';
            } elseif ($request->is_accepted == 1) {
                $status_message = 'accepted';
            }

            if ($verification_request->type == 'product_verification') {
                $product = Product::find($verification_request->product_id);

                if ($product->is_verified == True) {
                    return $this->error("This product was previously verified");
                } elseif ($request->is_accepted == True) {
                    $product->is_verified = True;
                    $product->save();
                } elseif ($request->is_accepted == False) {
                    $action = 'Verification request #' .$verification_request->id. ' was ' .$status_message;
                    $notes = 'Product id ' .$product->id.' with EAN ' .$product->ean. ' is now deleted';
                    $admin_log_repository = new AdminLogRepository();
                    $create_admin_log = $admin_log_repository->createAdminLog($action, $notes);

                    if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                        return $this->error($create_admin_log->getData()->message);
                    }

                    // Delete all occurrences of product when request is rejected
                    $find_product_price = Price::where('product_id', '=', $product->id)->get();

                    for ($i = 0; $i < $find_product_price->count(); $i++) {
                        $price_repository = new PriceRepository();
                        $price_repository->deletePrice($find_product_price[$i]['id']);
                    }
                    $find_verification_requests = VerificationRequest::where('product_id', '=', $product->id)->first();
                    $this->deleteVerificationRequest($find_verification_requests['id']);

                    $product_repository = new ProductRepository();
                    $product_repository->deleteProduct($product->id);

                    return $this->success("Verification request is rejected. Product is deleted", $product);
                }
            } elseif ($verification_request->type == 'merchant_verification' and $request->is_accepted == True) {
                $shop = Shop::where('user_id', '=', $verification_request->user_id)->first();

                if ($shop->is_verified == True) {
                    return $this->error("This merchant was previously verified");
                }

                $shop->is_verified = True;
                $shop->save();
            }

            $verification_request->status = 'Resolved';
            $verification_request->is_accepted = $request->is_accepted;
            $verification_request->save();

            $user = User::find($verification_request->user_id);

            if ($verification_request->type != 'product_verification') {
                Mail::to($user)->send(new VerificationRequestStatus($user, $verification_request));
            } elseif ($verification_request->type == 'product_verification') {
                if ($request->is_accepted == True) {
                    Mail::to($user)->send(new VerificationRequestStatus($user, $verification_request, $product));
                } else {
                    Mail::to($user)->send(new VerificationRequestStatus($user, $verification_request));
                }
            }

            if ($verification_request->type == 'product_verification') {
                $notes = 'Product id ' .$product->id.' with EAN ' .$product->ean. ' is now verified';
            } else {
                $notes = 'Verification request type: '.$verification_request->type;
            }

            $action = 'Verification request #' .$verification_request->id. ' was ' .$status_message;
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action, $notes);
    
            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Verification request status updated", $verification_request);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteVerificationRequest($id)
    {
        DB::beginTransaction();
        try {
            $verification_request = VerificationRequest::find($id);

            if (!$verification_request) {
                return $this->error("Verification request not found", 404);
            } elseif ($verification_request->type != 'product_verification') { // Remove file from public/images/uploads
                $file_path = str_replace("\\\\", "/", $verification_request->uploaded_file_path);
                unlink(public_path(). '/' .$file_path);
            }

            $verification_request->delete();

            DB::commit();
            return $this->success("Verification request deleted", $verification_request);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createVerificationRequest(Request $request)
    {
        try {
            $inputs = [
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'type' => $request->type,
                'document' => $request->document,
                'level' => $request->level,
                'select_file' => $request->file('select_file')
            ];
            $rules = [
                'user_id' => 'required|exists:users,id',
                'type' => ['required', Rule::in($this->type_choices)],
                'document' => 'required_if:type,merchant_verification,wallet_verification',
                'product_id' => 'nullable|required_if:type,product_verification|exists:products,id',
                'level' => 'nullable|required_if:type,wallet_verification|integer|min:1|max:3',
                'select_file' => 'nullable|required_if:type,merchant_verification,wallet_verification|image|mimes:jpeg,jpg,gif,png|max:3072',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $file_path = null;

            if ($request->type == 'merchant_verification' or $request->type == 'wallet_verification') {
                $file_name = 'user' .$request->user_id. '_' .time();
                $directory = env('PROOF_OF_PAYMENT_PATH');
                $upload_image = new UploadImage($request, 'select_file', $file_name, $directory);
                $file_path = $upload_image->upload();
            }

            $verification_request = VerificationRequest::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'type' => $request->type,
                'document' => $request->document,
                'level' => $request->level,
                'uploaded_file_path' => $file_path,
                'status' => 'Unresolved',
                'is_accepted' => False
            ]);

            return $this->success("Verification request created", $verification_request);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
