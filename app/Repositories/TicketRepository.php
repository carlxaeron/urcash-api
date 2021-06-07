<?php

namespace App\Repositories;

use App\Ticket;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use App\Interfaces\TicketInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TicketRepository implements TicketInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllTickets()
    {
        try {
            $tickets = Ticket::all()->where('status','=',true);

            if ($tickets->count() < 1) {
                return $this->error("Tickets not found", 404);
            }

            return $this->success("Tickets", $tickets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getTicketById($id)
    {
        try {
            $ticket = Ticket::find($id);

            if (!$ticket) return $this->error("Ticket not found", 404);

            return $this->success("Ticket", $ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createTicket(Request $request)
    {
        try {
            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 1
            ];
            $rules = [
                'title' => 'required',
                'description' => 'required',
                'amount' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $qrCode = new GenerateRandomIntegers(1, 9, 6); //initialize GenerateRandomIntegers

            $ticket = Ticket::create([
                'qr_code' => $qrCode->generate(),
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 1
            ]);

            return $this->success("Ticket created", $ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);

            if (!$ticket) return $this->error("Ticket not found", 404);

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

            return $this->success("Ticket updated", $ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteTicket($id)
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

    public function getTicketByQRCode($qr_code)
    {
        try {
            $ticket = Ticket::where('qr_code', $qr_code)->first();

            if (!$ticket) return $this->error("Ticket not found", 404);

            return $this->success("Ticket", $ticket);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
