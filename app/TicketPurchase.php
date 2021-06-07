<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketPurchase extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id', 'number_of_tickets', 'total', 'pin_code_1', 'pin_code_2', 'status',
    ];

    /**
     * Get the user associated with the announcement.
     */
    public function user() {
        return $this->belongsTo('App\User');
    }

    public function ticket() {
        return $this->hasOne('App\ticket');
    }

}
