<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    protected $fillable = [
        'qr_code','title', 'description', 'amount', 'status',
    ];

    public function ticketPurchase()
    {
        return $this->belongsTo('App\TicketPurchase');
    }
}
