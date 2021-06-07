<?php

namespace App\Repositories;

use App\Role;
use App\SupportTicket;
use App\User;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use App\Interfaces\SupportTicketInterface;
use App\Mail\CreateSupportTicket;
use App\Mail\MarkSupportTicketAsResolved;
use App\Mail\RespondToSupportTicket;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupportTicketRepository implements SupportTicketInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    private $issue_choices = ['Account', 'Cashout', 'Pay QR', 'Scan QR', 'Verification', 'Voucher', 'Others'];
    private $issue_choices_lowercase = ['account', 'cashout', 'pay_qr', 'scan_qr', 'verification', 'voucher', 'others'];
    private $priority_choices = ['Low', 'Medium', 'High', 'Urgent'];
    private $priority_choices_lowercase = ['low', 'medium', 'high', 'urgent'];
    private $status_choices = ['open', 'resolved'];

    public function getAllSupportTickets()
    {
        try {
            $support_tickets = SupportTicket::all();

            if ($support_tickets->count() < 1) {
                return $this->error("Support tickets not found", 404);
            }

            return $this->success("All support tickets", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketById($id)
    {
        try {
            $support_ticket = SupportTicket::find($id);

            if (!$support_ticket) return $this->error("Support ticket not found", 404);

            return $this->success("Support ticket detail", $support_ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByAuthUserAndStatus($status)
    {
        try {
            $user = Auth::user();

            if (!$user) return $this->error("You are not authenticated", 401);

            $support_tickets_user = SupportTicket::where('mobile_number', '=', $user->mobile_number)->get();

            if ($support_tickets_user->count() < 1) {
                return $this->error("Support tickets not found for user $user->id", 404);
            }

            $inputs = ['status' => $status];
            $rules = ['status' => ['required', Rule::in($this->status_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($status == 'open') {
                $is_resolved = False;
            } else {
                $is_resolved = True;
            }

            $support_tickets = SupportTicket::where('mobile_number', '=', $user->mobile_number)
                ->where('is_resolved', '=', $is_resolved)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1 and $is_resolved == True) {
                return $this->error("No support tickets have been resolved yet", 404);
            } elseif ($tickets_count < 1 and $is_resolved == False) {
                return $this->error("No support tickets are currently open", 404);
            }

            return $this->success("Found $tickets_count $status support ticket(s) for user $user->id", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getAllAccountLockRequestReviewSupportTickets()
    {
        try {
            $support_tickets = SupportTicket::where('issue', '=', 'Account')->where('is_resolved', '=', False)->get();

            return $this->success("All pending account issue support tickets", array(
                "count" => $support_tickets->count(),
                "support_tickets" => $support_tickets
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsAssignedToCustomerAgent($user_id)
    {
        try {
            $inputs = ['user_id' => $user_id];
            $rules = ['user_id' => 'required|integer|exists:users,id'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $support_tickets = SupportTicket::where('assigned_to_user', '=', $user_id)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1) {
                return $this->error("No support tickets are assigned to user $user_id", 404);
            }

            return $this->success("Found $tickets_count support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketByReferenceNumber($reference_number)
    {
        try {
            $support_ticket = SupportTicket::where('reference_number', '=', $reference_number)->first();

            if (!$support_ticket) return $this->error("Support ticket not found", 404);

            return $this->success("Support ticket detail", $support_ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByMobileNumber($mobile_number)
    {
        try {
            $inputs = ['mobile_number' => $mobile_number];
            $rules = ['mobile_number' => 'required|integer|starts_with:63|digits:12|exists:users,mobile_number'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $support_tickets = SupportTicket::where('mobile_number', '=', $mobile_number)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1) {
                return $this->error("No support tickets are found for $mobile_number", 404);
            }

            return $this->success("Found $tickets_count support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByEmail($email)
    {
        try {
            $inputs = ['email' => $email];
            $rules = ['email' => 'required'];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $support_tickets = SupportTicket::where('email', '=', $email)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1) {
                return $this->error("No support tickets are found for $email", 404);
            }

            return $this->success("Found $tickets_count support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByIssue($issue)
    {
        try {
            $inputs = ['issue' => $issue];
            $rules = ['issue' => ['required', Rule::in($this->issue_choices_lowercase)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($issue == 'pay_qr') {
                $issue = 'Pay QR';
            } elseif ($issue == 'scan_qr') {
                $issue = 'Scan QR';
            } else {
                $issue = Str::title($issue);
            }

            $support_tickets = SupportTicket::where('issue', '=', $issue)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1) {
                return $this->error("No support tickets are found for issue $issue", 404);
            }

            return $this->success("Found $tickets_count $issue issue support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByPriority($priority)
    {
        try {
            $inputs = ['priority' => $priority];
            $rules = ['priority' => ['required', Rule::in($this->priority_choices_lowercase)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $priority = Str::title($priority);
            $support_tickets = SupportTicket::where('priority', '=', $priority)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1) {
                return $this->error("No support tickets are found with priority $priority", 404);
            }

            return $this->success("Found $tickets_count support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getSupportTicketsByStatus($status)
    {
        try {
            $inputs = ['status' => $status];
            $rules = ['status' => ['required', Rule::in($this->status_choices)]];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($status == 'open') {
                $is_resolved = False;
            } else {
                $is_resolved = True;
            }

            $support_tickets = SupportTicket::where('is_resolved', '=', $is_resolved)->get();
            $tickets_count = $support_tickets->count();

            if ($tickets_count < 1 and $is_resolved == True) {
                return $this->error("No support tickets have been resolved yet", 404);
            } elseif ($tickets_count < 1 and $is_resolved == False) {
                return $this->error("No support tickets are currently open", 404);
            }

            return $this->success("Found $tickets_count $status support ticket(s)", $support_tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function respondToSupportTicket(Request $request)
    {
        try {
            $inputs = [
                'reference_number' => $request->reference_number,
                'message' => $request->message,
            ];
            $rules = [
                'reference_number' => 'required|exists:support_tickets,reference_number',
                'message' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $support_ticket = SupportTicket::where('reference_number', '=', $request->reference_number)->first();

            Mail::to($support_ticket->email)->send(new RespondToSupportTicket($support_ticket, $request->message));

            return $this->success("Support ticket replied", $support_ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateSupportTicket($id)
    {
        try {
            $support_ticket = SupportTicket::find($id);

            if (!$support_ticket) {
                return $this->error("Support ticket not found", 404);
            } elseif ($support_ticket->is_resolved == True) {
                return $this->error("Support ticket was previously marked as resolved");
            }

            $support_ticket->is_resolved = True; // Update status of support ticket as resolved
            $support_ticket->save();

            if ($support_ticket->issue == "Account") { // Unlock account
                $find_user = User::where('mobile_number', '=', $support_ticket->mobile_number)->first();
                $find_user->is_locked = False;
                $find_user->save();
            }

            $user = User::where('email', '=', $support_ticket->email)->first();

            if ($user) { // If email is registered, use first name of User
                Mail::to($user->email)->send(new MarkSupportTicketAsResolved($user->first_name, $support_ticket));
            } else { // If email is not registered, use name from Support Ticket
                Mail::to($support_ticket->email)->send(new MarkSupportTicketAsResolved($support_ticket->name, $support_ticket));
            }

            $action = 'Support ticket #' .$support_ticket->reference_number. ' was marked as resolved';
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Support ticket marked as resolved", $support_ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteSupportTicket($id)
    {
        DB::beginTransaction();
        try {
            $support_ticket = SupportTicket::find($id);

            if (!$support_ticket) return $this->error("Support ticket not found", 404);

            $support_ticket->delete();

            DB::commit();
            return $this->success("Support ticket deleted", $support_ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createSupportTicket(Request $request)
    {
        try {
            $inputs = [
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'issue' => $request->issue,
                'priority' => $request->priority,
                'description' => $request->description
            ];
            $rules = [
                'issue' => ['required', Rule::in($this->issue_choices)],
                'name' => 'required',
                'mobile_number' => 'nullable|required_if:issue,Account|starts_with:63|digits:12|exists:users,mobile_number',
                'email' => 'required|email',
                'priority' => ['nullable', Rule::in($this->priority_choices)],
                'description' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if (!$request->priority || $request->priority == null) { // Set priority as Low by default if field is null
                $request->priority = "Low";
            }

            $find_customer_support_role = Role::where('slug', '=', 'customer-support')->first();

            if (!$find_customer_support_role) {
                return $this->error("Customer support role does not exist", 404);
            }

            // Get random users whose role is customer support
            $customer_support_role = Role::find($find_customer_support_role->id)->users()->inRandomOrder();

            if ($customer_support_role->count() < 1) {
                return $this->error("There are no users with the customer support role", 404);
            }

            $random_customer_support_user = $customer_support_role->first(); // Get first object

            $generate_random_integers = new GenerateRandomIntegers(1, 9, 7); // Instantiate
            $reference_number = $generate_random_integers->generate();

            $support_ticket = SupportTicket::create([
                'assigned_to_user' => $random_customer_support_user->id,
                'reference_number' => $reference_number,
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'issue' => $request->issue,
                'priority' => $request->priority,
                'description' => $request->description,
                'is_resolved' => False,
            ]);

            $user = User::where('email', '=', $request->email)->first();

            if ($user) { // If email is registered, use first name of User
                Mail::to($user->email)->send(new CreateSupportTicket($user->first_name, $support_ticket));
            } else { // If email is not registered, use name of input
                Mail::to($request->email)->send(new CreateSupportTicket($request->name, $support_ticket));
            }

            return $this->success("Support ticket created", $support_ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
