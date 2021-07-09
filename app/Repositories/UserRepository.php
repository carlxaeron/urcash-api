<?php

namespace App\Repositories;

use App\Address;
use App\Role;
use App\Shop;
use App\User;
use App\VerificationCode;
use App\VoucherAccount;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use App\Http\Helper\Utils\Helper;
use App\Http\Helper\Utils\UploadImage;
use App\Http\Resources\PurchaseItems;
use App\Http\Resources\User as ResourcesUser;
use App\Http\Services\NexmoService\SendService;
use App\Http\Services\RedService;
use App\Interfaces\UserInterface;
use App\Mail\HasTooManyLoginAttempts;
use App\Mail\VerifyEmail;
use App\Product;
use App\PurchaseItem;
use App\Repositories\ShopRepository;
use App\Repositories\SupportTicketRepository;
use App\Traits\ResponseAPI;
use App\UserCart;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\UsersRole;
use Exception;

class UserRepository implements UserInterface
{
    // Use ResponseAPI and ThrottlesLogins traits in this repository
    use ResponseAPI, ThrottlesLogins;

    protected $maxAttempts = 3; // Set maximum failed login attempts to 3
    protected $tokens;

    public function username() { // Override default username in ThrottlesLogins trait for validation
        return 'mobile_number';
    }

    public function loginV1WithRed(Request $request){
        try {
            $inputs = [
                'email' => $request->email,
                'password' => $request->password,
            ];
            $rules = [
                'email' => 'required',
                'password' => 'required'
            ];

            $validation = Validator::make($inputs, $rules, [
                'email.required' => 'Username is required'
            ]);

            if ($validation->fails()) return $this->error($validation->errors()->all());
            else {
                $resp = app(RedService::class)->login($request);
                if($resp['status'] == 'error') {
                    return $this->error($resp['message'],$resp['code']);
                }
                else if($resp['status'] == 'success') {
                    $user = User::where('email', $resp['message'][0]['email'])->first();

                    if(!$user) {
                        $_resp = $resp['message'][0];
                        return $this->success(RedService::$ERR_SUCCESS_NOT_YET_REGISTERED,$_resp,202);
                    } else {
                        $token = array(
                            'token' => $user->createToken('Auth Token')->accessToken,
                            'user'=>$user
                        );
                        
                        return $this->success("Login success", $token);
                    }
                }
                else {
                    return $this->error('Unknown Error. Please try again.');
                }
            }
            

        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function loginV1(Request $request)
    {
        try {
            $inputs = [
                'email' => $request->email,
                'password' => $request->password,
            ];
            $rules = [
                'email' => 'required|email:rfc,strict|exists:users,email',
                'password' => 'required'
            ];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());
            $user = User::where('email', $request->email)->with(['userRoles.role'])->first();

            if(!Hash::check($request->password, $user->password)) return $this->error('Invalid Password.');

            if(!$user) return $this->error('Email is not yet registered.');
            else {
                if (!$user->is_locked) {
                    $token = array(
                        'token' => $user->createToken('Auth Token')->accessToken,
                        'user'=>new ResourcesUser($user)
                    );

                    return $this->success("Login success", $token);
                }
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function login(Request $request)
    {
        try {
            $inputs = ['mobile_number' => $request->mobile_number];
            $rules = ['mobile_number' => 'required|numeric|digits:12'];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::where('mobile_number', $request->mobile_number)->first();

            if (!$user) {
                return $this->error("Your number is not registered.");
            } else {
                if (!$user->is_locked) {
                    // Update user OTP
                    $sendCode = new SendService; // Send SMS service
                    DB::update(
                        'UPDATE users SET otp = ?, otp_expiration = DATE_ADD(NOW(), INTERVAL 5 MINUTE), otp_created_at = NOW() WHERE mobile_number = ?',
                        [$sendCode->sendCode($request->mobile_number), $request->mobile_number]
                    );
                    return $this->success("Login success", true);
                }
            }
            return $this->success("Login success", false);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function verifyMerchant(Request $request)
    {
        try {
            $inputs = [
                'id' => $request->id,
            ];
            $rules = [
                'id' => 'required|exists:users,id',
            ];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::find($request->id);

            if($user->merchant_level > 0) return $this->error('User is already merchant');
            elseif(!$user->hasRole('merchant')) return $this->error('User role cannot be as merchant');

            $user->status = 1;
            $user->save();

            return $this->success('User successfully as merchant', $user);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllUsers()
    {
        try {
            $users = User::all();

            return $this->success("All users", $users);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllUsersV1()
    {
        try {
            $users = User::with(['roles']);
            
            $users = request()->page ? $users->paginate(request()->per_page ? request()->per_page : 10) : $users->get();

            return $this->success("All users", new ResourcesUser($users));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUserById($id)
    {
        try {
            $user = User::find($id);

            if (!$user) return $this->error("No user with ID $id", 404);

            return $this->success("User detail", $user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function requestReviewUnlockAccount(Request $request)
    {
        try {
            $inputs = [
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'birthdate' => $request->birthdate
            ];
            $rules = [
                'mobile_number' => 'required|integer|starts_with:63|digits:12|exists:users,mobile_number',
                'email' => 'required|email|exists:users,email',
                'birthdate' => 'required|date|max:10|before:today'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Must match mobile_number, email and birthdate
            $user = User::where('mobile_number', $request->mobile_number)
                ->where('email', '=', $request->email)
                ->where('birthdate', '=', $request->birthdate)->first();

            if (!$user) {
                return $this->error("The information you've submitted don't match our records. Please try again.", 404);
            } elseif ($user->is_locked == False) {
                return $this->error("Your account is not locked");
            }

            $request->name = Str::title($user->first_name. " " .$user->last_name);
            $request->issue = 'Account';
            $request->priority = 'Medium';
            $request->description = 'Birthday: ' .$request->birthdate;

            $support_ticket_repository = new SupportTicketRepository(); // Instantiate
            $support_ticket = $support_ticket_repository->createSupportTicket($request);

            if ($support_ticket->getData()->statusCode == 500) {
                return $this->error($support_ticket->getData()->message);
            }

            return $this->success("Review request submitted!", $support_ticket->getData()->results);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function setMpin(Request $request)
    {
        try {
            $inputs = [
                'mobile_number' => $request->mobile_number,
                'password' => $request->password,
            ];
            $rules = [
                'mobile_number' => 'required|integer|starts_with:63|digits:12|exists:users,mobile_number',
                'password' => 'required|integer|digits:4',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Prevent setting MPIN of another user. Set only the authenticated user's own MPIN
            if (Auth::user()->mobile_number != $request->mobile_number) {
                return $this->error("Unauthorized access", 401);
            }

            $user = User::where('mobile_number', '=', $request->mobile_number)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            return $this->success("MPIN successfully set", $user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function sendResetPasswordLinkEmail(Request $request)
    {
        try {
            $inputs = ['mobile_number' => $request->mobile_number];
            $rules = ['mobile_number' => 'required|numeric|starts_with:63|digits:12|exists:users,mobile_number'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // We will send the password reset link to this user. Once we have attempted to send the link, we will
            // examine the response then see the message we need to show. Finally, we'll send out a proper response
            $response = Password::broker()->sendResetLink($request->only('mobile_number'));

            // Send error when user has recently requested for a password reset link. Requests are throttled for 1 minute
            if ($response == 'passwords.throttled') {
                return $this->error("You have requested password reset recently. Please check your email", 429);
            }

            $user = User::where('mobile_number', '=', $request->mobile_number)->first();
            $request->email = $user->email;

            return $this->success("Password reset link successfully sent to email", array("email" => $user->email));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function sendResetPasswordLinkEmailV1(Request $request)
    {
        try {
            $inputs = ['email' => $request->email];
            $rules = ['email' => 'required|email|exists:users,email'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // We will send the password reset link to this user. Once we have attempted to send the link, we will
            // examine the response then see the message we need to show. Finally, we'll send out a proper response
            $response = Password::broker()->sendResetLink($request->only('email'));

            // Send error when user has recently requested for a password reset link. Requests are throttled for 1 minute
            if ($response == 'passwords.throttled') {
                return $this->error("You have requested password reset recently. Please check your email", 429);
            }

            $user = User::where('email', '=', $request->email)->first();
            $request->email = $user->email;

            return $this->success("Password reset link successfully sent to email", array("email" => $user->email));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function resetPasswordWithToken(Request $request)
    {
        try {
            $inputs = [
                'mobile_number' => $request->mobile_number,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token
            ];
            $rules = [
                'mobile_number' => 'required|numeric|starts_with:63|digits:12|exists:users,mobile_number',
                'password' => 'required|integer|same:password_confirmation', // Must be the same with input name: "password_confirmation"
                'token' => 'required' // Must match the token received on email. This value is found between 'reset/' and '?email='
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Get the password reset credentials from the request. Here we will attempt to reset the user's password.
            // If it is successful we will update the password on an actual user model and persist it to the database.
            $response = Password::broker()->reset(
                $request->only('mobile_number', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->remember_token = Str::random(60);
                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            // Send error when user has recently reset password and this token is already expired after resetting it
            if ($response == 'passwords.token') return $this->error("Token has expired.");

            $user = User::where('mobile_number', '=', $request->mobile_number)->first();

            return $this->success("Your password has been reset", $user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function resetPasswordWithTokenV1(Request $request)
    {
        try {
            $inputs = [
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token
            ];
            $rules = [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|same:password_confirmation|min:8', // Must be the same with input name: "password_confirmation"
                'token' => 'required' // Must match the token received on email. This value is found between 'reset/' and '?email='
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Get the password reset credentials from the request. Here we will attempt to reset the user's password.
            // If it is successful we will update the password on an actual user model and persist it to the database.
            $response = Password::broker()->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    // dd($user, $password);
                    if(Hash::needsRehash($user->password)) {
                        $user->password = Hash::make($password);
                    } else {
                        $user->password = Hash::make($password);
                    }
                    $user->remember_token = Str::random(60);
                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            // Send error when user has recently reset password and this token is already expired after resetting it
            if ($response == 'passwords.token') return $this->error("Token has expired.");

            $user = User::where('email', '=', $request->email)->first();

            return $this->success("Your password has been reset", $user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function registerUserV1(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'birth_date' => $request->birth_date,
                'password' => $request->password,
                'repeat_password' => $request->repeat_password,
                'code' => $request->code,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'street' => $request->street,
                'country' => $request->country,
            ];
            $rules = [
                'last_name' => 'required',
                'first_name' => 'required',
                'middle_name' => 'required',
                'mobile_number' => ['required','numeric','starts_with:63','digits:12',function($attr,$value,$fail){
                    if(User::where('mobile_number',$value)->first()) $fail("Sorry! The $attr is already exists!");
                }],
                'email' => ['required','email',function($attr,$value,$fail){
                    if(User::where('email',$value)->first()) $fail("Sorry! The $attr is already exists!");
                }],
                'birth_date' => 'required|date',
                'password' => 'required|min:6',
                'repeat_password' => 'required|same:password',
                'province' => 'required',
                'city' => 'required',
                'barangay' => 'required',
                'street' => 'required',
                'country' => 'required',
            ];
            if($request->code) {
                $rules['code'] = ['sometimes',function($attr,$value,$fail) {
                    if($value !== 'RED') $fail('Invalid Code');
                }];
            }

            if($request->code == 'RED') {
                unset($rules['password']);
                unset($rules['repeat_password']);
            }

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $inputs['birthdate'] = $inputs['birth_date'];

            if($request->code == 'RED') {
                // Create User
                $user = User::create($inputs);
                $user->status = 1;
                $user->save();

                // Role
                UsersRole::create(['user_id'=>$user->id,'role_id'=>Role::where('slug','merchant')->first()->id]);

                // Address
                $address = Address::create($inputs);

                $user->address_id = $address->id;
                $user->save();

                $token = array(
                    'token' => $user->createToken('Auth Token')->accessToken,
                    'user'=>$user
                );

                DB::commit();

                // Refresh relationships
                $user = $user->with(['userRoles.role'])->find($user->id);

                return $this->success("Successfully created!", $token);
            } else {
                // Create User
                $user = User::create($inputs);

                // Role
                UsersRole::create(['user_id'=>$user->id,'role_id'=>Role::where('slug','merchant')->first()->id]);

                // Address
                $address = Address::create($inputs);

                $user->address_id = $address->id;
                $user->save();

                $otp = app(Helper::class)->generateCode();
                $user->otp = $otp;
                $user->save();
    
                // Send verification email with code
                Mail::to($user)->send(new VerifyEmail($user, $otp));
                
                DB::commit();

                // Refresh relationships
                $user = $user->with(['userRoles.role'])->find($user->id);
            }
            return $this->success("Successfully created!", new ResourcesUser($user));
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function registerUser(Request $request)
    {
        try {
            $inputs = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'mobile_number' => $request->mobile_number,
                'email' => preg_replace('/\s+/', '', $request->email),
                'birthdate' => $request->birthdate,
                'street' => $request->street1,
                'barangay' => $request->barangay,
                'city' => $request->city,
                'province' => $request->province,
                'dti' => $request->dti,
                'bir_reg_cert' => $request->bir_reg_cert,
                'mayors_permit' => $request->mayors_permit,
                'reg_bus_name' => $request->reg_bus_name,
                'bus_address' => $request->bus_address,
                'bus_city' => $request->bus_city,
                'bus_province' => $request->bus_province,
                'password' => $request->password
            ];
            $rules = [
                'first_name' => 'required',
                'middle_name' => 'required',
                'last_name' => 'required',
                'mobile_number' => 'required|numeric|digits:12|unique:users',
                'email' => 'required|email|unique:users',
                'birthdate' => 'required|date|max:10|before:today',
                'street' => 'required',
                'barangay' => 'required',
                'city' => 'required',
                'province' => 'required',
                'dti' => 'required_without_all:bir_reg_cert,mayors_permit|max:8',
                'bir_reg_cert' => 'required_without_all:dti,mayors_permit|max:13',
                'mayors_permit' => 'required_without_all:dti,bir_reg_cert|max:16',
                'reg_bus_name' => 'required',
                'bus_address' => 'required',
                'bus_city' => 'required',
                'bus_province' => 'required',
                'password' => 'required|digits:4'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Create Address
            $address = Address::create([
                'street' => $request->street1,
                'barangay' => $request->barangay,
                'city' => $request->city,
                'province' => $request->province,
                'country' => 'Philippines'
            ]);

            $inputs['status'] = true;
            $inputs['is_locked'] = false;
            $inputs['address_id'] = $address['id'];
            $inputs['password'] = Hash::make($request->password);

            // Create User
            $user = User::create($inputs);

            // Create Address for Shop
            $shop_address = Address::create([
                'complete_address' => $request->bus_address,
                'barangay' => $request->barangay,
                'city' =>  $request->bus_city,
                'province' =>  $request->bus_province,
                'country' => 'Philippines'
            ]);

            $request->user_id = $user->id;
            $request->address_id = $shop_address->id;
            // Create Shop
            $shop_repository = new ShopRepository();
            $shop = $shop_repository->createShop($request)->getData()->results;

            VoucherAccount::create(array(
                'shop_id' => $shop->id,
                'voucher_balance' => 0.00
            ));

            $generate_random_integers = new GenerateRandomIntegers(1, 9, 6);
            $code = $generate_random_integers->generate();
            $verification_code = VerificationCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'is_verified' => False,
            ]);

            $merchant_role = Role::where('slug', '=', 'merchant')->first();
            $user->roles()->attach($merchant_role); // Add merchant role

            // Send verification email with code
            Mail::to($user)->send(new VerifyEmail($user, $verification_code->code));

            // $qrCode = new GenerateRandomIntegers(1, 9, 12);
            // DB::table('wallets')->insert([
            //     'qr_code' => $qrCode->generate(),
            //     'user_id' => (int) $user->id,
            //     'available_balance' => 0
            // ]);

            $sendCode = new SendService;
            $otp = $sendCode->sendCode($inputs['mobile_number']);
            DB::update(
                'UPDATE users SET otp = ?, otp_expiration = DATE_ADD(NOW(), INTERVAL 5 MINUTE), otp_created_at = NOW() WHERE mobile_number = ?',
                [$otp, $inputs['mobile_number']]
            );

            return $this->success("New user created", array("mobile_number" => $inputs['mobile_number']));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $address = Address::find($user->address_id)->first();

            if (!$user) return $this->error("User not found");

            $inputs = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'birthdate' => $request->birthdate,
                'street' => $request->street,
                'barangay' => $request->barangay,
                'city' => $request->city,
                'province' => $request->province
            ];
            $rules = [
                'first_name' => 'required',
                'middle_name' => 'required',
                'last_name' => 'required',
                'birthdate' => 'required|date|max:10|before:today',
                'street' => 'required',
                'barangay' => 'required',
                'city' => 'required',
                'province' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user->update($inputs);
            $address->update($inputs);

            return $this->success("User updated", array("user" => $inputs));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            if (!$user) return $this->error("No user with ID $id", 404);

            $user->status = false;
            $user->update(); // Delete the user

            DB::commit();
            return $this->success("User deleted", $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out',
                'error' => false,
                'statusCode' => 200
            ]);
        } else {
            return $this->error("User is not logged in", 401);
        }
    }

    public function getUserByMobileNumber($mobile_number)
    {
        try {
            $user = User::where('mobile_number', $mobile_number)->first();

            if (!$user) return $this->error("User not found");

            $shop = Shop::where('user_id', '=', $user->id)->first();
            $address = Address::find($user->address_id);
            $shop_address =  Address::find($shop->address_id);
            $voucher_account = VoucherAccount::where('shop_id', '=', $shop->id)->first();

            return $this->success("User information", array(
                'birthdate' => $user->birthdate,
                'user' => $user,
                'shop' => $shop,
                'address' => $address,
                'shop_address' => $shop_address,
                'voucher_account' => $voucher_account
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getUserInfo()
    {
        try {
            $user = User::with(['address','userRoles.role'])->find(Auth::user()->id);
    
            if (!$user) return $this->error("User not found");
    
            return $this->success("User information", new ResourcesUser($user));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateUserAddress(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'street'=>$request->street,
                'barangay'=>$request->barangay,
                'city'=>$request->city,
                'province'=>$request->province,
                'country'=>$request->country,
            ];
            $rules = [
                'street'=>'required',
                'barangay'=>'required',
                'city'=>'required',
                'province'=>'required',
                'country'=>'required',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::find(Auth::user()->id);

            if($user->address_id) {
                $address = Address::find($user->address_id);
                if($address) {
                    $address->street = $request->street;
                    $address->barangay = $request->barangay;
                    $address->city = $request->city;
                    $address->province = $request->province;
                    $address->country = $request->country;
                    $address->save();
                    $msg = "Address Successfully updated!";
                } else {
                    $address = Address::create($inputs);
                    $user_ = User::find($user->id);
                    $user_->address_id = $address->id;
                    $user_->save();
                    $msg = "Address Successfully created!";
                }
            } else {
                $address = Address::create($inputs);
                $user_ = User::find($user->id);
                $user_->address_id = $address->id;
                $user_->save();
                $msg = "Address Successfully created!";
            }

            DB::commit();

            $user = User::find($user->id);

            return $this->success($msg, new ResourcesUser($user));
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function updateUserAvatar(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'picture'=>$request->picture
            ];
            $rules = [
                'picture'=>'required|image',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::find(Auth::user()->id);

            $user->profile_picture = $request->picture->store('image/profile');
            $user->save();

            DB::commit();

            $user = User::find($user->id);

            return $this->success('Successfully updated your profile picture.', new ResourcesUser($user));
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function getUserPurchases()
    {
        try {
            $user = Auth::user();

            $purchases = PurchaseItem::with(['product.categories.category'])->where('user_id', $user->id);

            $purchases = request()->page ? $purchases->paginate(request()->per_page ? request()->per_page : 10) : $purchases->get();

            return $this->success("User purchases", new PurchaseItems($purchases));
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateOtpAndSend($mobile_number)
    {
        try {
            $sendCode = new SendService;
            DB::update(
                'UPDATE users SET otp = ?, otp_expiration = DATE_ADD(NOW(), INTERVAL 5 MINUTE), otp_created_at = NOW() WHERE mobile_number = ?',
                [$sendCode->sendCode($mobile_number), $mobile_number]
            );

            return $this->success("Resend code successful!", true);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function validateOtpV1(Request $request)
    {
        $inputs = [
            'email' => $request->email,
            'otp' => $request->otp
        ];
        $rules = [
            'email' => 'required',
            'otp' => 'required|numeric|digits:4'
        ];
        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) return $this->error($validation->errors()->all());

        $user = User::where('email',$request->email)->where('otp',$request->otp)->first();

        if(!$user) return $this->error('Invalid Code. Please try again.');

        $user->email_verified_at = now();
        $user->save();

        $token = array(
            'token' => $user->createToken('Auth Token')->accessToken,
            'user'=>$user
        );

        return $this->success("OTP validation successful", $token);
    }

    public function validateOtp(Request $request)
    {
        $inputs = [
            'mobile_number' => $request->mobile_number,
            'otp' => $request->otp
        ];
        $rules = [
            'mobile_number' => 'required|numeric|digits:12',
            'otp' => 'required|numeric|digits:4'
        ];
        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) return $this->error($validation->errors()->all());

        $user = User::where('mobile_number', $request->mobile_number)->first();

        // $error_message = "Your account is locked. Please contact customer support to retrieve your account.";

        // if ($user->is_locked == True) {
        //     return $this->error("Sorry. " .$error_message);
        // } elseif ($this->hasTooManyLoginAttempts($request)) {
        //     $this->fireLockoutEvent($request);
        //     if ($user) {
        //         Mail::to($user)->send(new HasTooManyLoginAttempts($user));
        //     }

        //     $user->is_locked = True; // Lock user account upon 3 failed login attempts
        //     $user->save();

        //     return $this->error("Sorry. You have " .$this->maxAttempts. " failed attempts. " .$error_message, 429);
        // }

        // if (!$user) {
        //     $this->incrementLoginAttempts($request);

        //     return $this->error("User not found", 404);
        // }

        // // Get current time UPON EXECUTION of this validateOtp function
        // $today = Carbon::now()->format('Y-m-d h:i:s'); // eg. 2020-09-04 05:33:04 (12-hour format)

        // // Avoid validating OTP again when it's already validated. They must login to generate a new OTP code
        // if ($user->otp_expiration == $user->otp_created_at) {
        //     return $this->error("You have previously validated your OTP.", 500);
        // } elseif ($user->otp_created_at > $user->otp_expiration) { // if current time is greater than time of OTP expiration
        //     $this->incrementLoginAttempts($request);

        //     return $this->error("OTP has expired. OTPs are valid for 5 minutes.", 500);
        // } elseif ($request->otp != $user->otp) {
        //     $this->incrementLoginAttempts($request);

        //     return $this->error("Sorry, the code you have entered is invalid.", 500);
        // }

        // Ensures that correct OTP codes are validated once. Alter otp_expiration to prevent user from verifying
        // an already validated OTP when this function is executed again
        $user->otp_expiration = $user->otp_created_at;
        $user->save();

        $this->clearLoginAttempts($request);
        $token = array(
            'token' => $user->createToken('Auth Token')->accessToken
        );

        return $this->success("OTP validation successful", $token);
    }

    public function resetPassword(Request $request)
    {
        try {
            $inputs = [
                'password' => $request->password,
                'confirm_password' => $request->confirm_password,
                'mobile_number' => $request->mobile_number
            ];
            $rules = array(
                'password' => 'required',
                'confirm_password' => 'required|same:password',
                'mobile_number' => 'required'
            );
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            DB::update('UPDATE users SET password = ? WHERE mobile_number = ?',
                [Hash::make($request->password), $request->mobile_number]);

            return $this->success("Confirm password success", "OK");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function mpinLogin(Request $request)
    {
        try {
            $inputs = [
                'mobile_number' => $request->mobile_number,
                'password' => $request->password,
            ];
            $rules = [
                'mobile_number' => 'required|digits:12',
                'password' => 'required|integer|digits:4',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::where('mobile_number', '=', $request->mobile_number)->first();

            if (!$user){
                return $this->error("User not found.", 404);
            } elseif (!Hash::check($request->password, $user->password)) {
                return $this->error("Incorrect mpin. Please try again.");
            }

            $token = array(
                'token' => $user->createToken('Auth Token')->accessToken
            );

            return $this->success("Login success", $token);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateProfilePicture(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->error("User not found", 404);
            } elseif ($user->profile_picture != null) { // Replace previous profile picture if it exists
                $profile_picture_path = str_replace("\\\\", "/", $user->profile_picture);
                unlink(public_path(). '/' .$profile_picture_path);
            }

            $file_name = $user->first_name. '_' .$user->last_name. '_' .$user->mobile_number;
            $directory = '/images/profile-pictures/';

            $upload_image = new UploadImage($request, 'select_file', $file_name, $directory);
            $file_path = $upload_image->upload();

            $user['profile_picture'] = $file_path;
            $user->save();

            return $this->success("Profile picture successfully updated", ['file_path' => $file_path]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function registerAdmin(Request $request)
    {
        try {
            $inputs = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'mobile_number' => $request->mobile_number,
                'birthdate' => $request->birthdate,
                'email' => $request->email,
                'password' => $request->password
            ];
            $rules = [
                'first_name' => 'required',
                'middle_name' => 'required',
                'last_name' => 'required',
                'mobile_number' => 'required|numeric|digits:12|unique:users',
                'birthdate' => 'required|date|max:10|before:today',
                'email' => 'required|email|unique:users',
                'password' => 'required|digits:4'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $inputs['status'] = true;
            $inputs['is_locked'] = false;
            $inputs['password'] = Hash::make($request->password);

            // Create User
            $user = User::create($inputs);

            $administrator_role = Role::where('slug', '=', 'administrator')->first();
            $user->roles()->attach($administrator_role); // Add role

            $action = 'User ID ' .$user->id. ' created with ' .$administrator_role->name. ' role';
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("New admin created", array("admin" => $user));
        } catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateAdmin(Request $request, $id)
    {
        try {
            $user = User::find($id);

            $inputs = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'password' => $request->password
            ];
            $rules = [
                'first_name' => 'required',
                'middle_name' => 'required',
                'last_name' => 'required',
                'password' => 'required|digits:4'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user->update($inputs);

            return $this->success("Admin updated", array("admin" => $user));
        } catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAdmins()
    {
        try{
            $usersRole = UsersRole::all();
            $admins = [];
            foreach ($usersRole as $role) {

                if ($role->role_id == 1 || $role->role_id == 2) {
                    $user = User::find($role->user_id);
                    if ($user) {
                        $role = Role::find($role->role_id);
                        $admins[] = [
                            'id' => $user->id,
                            'birthdate' => $user->birthdate,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'middle_name' => $user->middle_name,
                            'mobile_number' => $user->mobile_number,
                            'role' => $role
                        ];
                    }
                }
            }

            return $this->success("Admins", array("admins" => $admins));
        }catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getCart(Request $request) {
        try{
            $user = Auth::user();

            $items = UserCart::where('user_id',$user->id)->get();

            return $this->success('Success fetched the cart.', $items);
        } catch(\Exception $e){
            DB::rollback();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    
    public function addToCart(Request $request) {
        DB::beginTransaction();
        try{
            $inputs = [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ];
            $rules = [
                'product_id' => ['required',function($attr,$value,$fail){
                    if(!Product::find($value)) $fail('Product does not exists.');
                }],
                'quantity' => 'required|integer',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = Auth::user();

            $cart = UserCart::where('user_id', $user->id)->where('product_id',$request->product_id)->first();
            if($cart) {
                if($request->quantity > 0) {
                    $cart->quantity = $cart->quantity + $request->quantity;
                    $cart->save();
                } else {
                    $cart->delete();
                }
            } else {
                UserCart::create(['user_id'=>$user->id,'product_id'=>$request->product_id,'quantity'=>$request->quantity]);
            }

            $items = UserCart::where('user_id',$user->id)->get();

            DB::commit();
            
            return $this->success('Success added to cart.', $items);
        }
        catch(\Exception $e){
            DB::rollback();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
