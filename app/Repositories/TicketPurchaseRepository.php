<?php

namespace App\Repositories;

use App\Interfaces\TicketPurchaseInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\TicketPurchase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\Utils\RandomStringGenerator;
use App\User;
use App\Ticket;
use App\Http\Services\DragonpayService;

class TicketPurchaseRepository implements TicketPurchaseInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllPurchaseTickets()
    {
        try {
            $tickets = TicketPurchase::all()->where('status','=',true);

            if ($tickets->count() < 1) {
                return $this->error("Ticket Purchase not found", 404);
            }

            return $this->success("Ticket Purchase", $tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPurchaseTicketById($id)
    {
        try {
            $ticket = TicketPurchase::find($id);

            if (!$ticket) return $this->error("Ticket Purchase not found", 404);

            return $this->success("Ticket Purchase", $ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createPurchaseTicket(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => ['required', 'numeric'],
                'no_of_tickets' => ['required', 'numeric'],
                'mobile_number' => ['required']
            ]);

            if ($validator->fails()) return $this->error($validator->errors()->all());

            $findUser = User::where('mobile_number', '=', $request->mobile_number)->first();
            $findTicket = Ticket::where('id','=',$request->ticket_id)->first();

            if (!$findUser && !$findTicket) {
                return $this->error('User and Ticket not found', 404);
            }
            $generator = new RandomStringGenerator; // Create new instance of generator class.

            $codeLength = 6; // Set token length.

            // Call method to generate random string.
            $code1 = strtoupper($generator->generate($codeLength));
            $code2 = strtoupper($generator->generate($codeLength));

            $ticketPurchase = TicketPurchase::create([
                'user_id' => $findUser->id,
                'ticket_id' => $request->ticket_id,
                'number_of_tickets' => $request->no_of_tickets,
                'total' =>$findTicket->amount * $request->no_of_tickets,
                'pin_code_1' => $code1,
                'pin_code_2' => $code2,
                'status' => 1,
            ]);

            return $this->success(
                "Ticket Purchase created",
                $ticketPurchase
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updatePurchaseTicket(Request $request, $id)
    {
        try {
            $ticket = TicketPurchase::find($id);

            if (!$ticket) return $this->error('Ticket Purchase not found', 404);

            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => $request->status
            ];
            $rules = [
                'title' => 'required',
                'description' => 'required',
                'amount' => 'required'
            ];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->amount = $request->amount;
            $ticket->update();

            return $this->success(
                "Ticket updated",
                $ticket
            );

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deletePurchaseTicket($id)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::find($id);

            // Check the user
            if (!$ticket) return $this->error("Ticket not found", 404);

            $ticket->status = false;
            // Delete the user
            $ticket->update();

            DB::commit();
            return $this->success("Ticket deleted", $ticket);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
