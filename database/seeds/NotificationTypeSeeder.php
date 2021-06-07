<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_types')->delete();

        DB::table('notification_types')->insert([
            [
                'notification_type_name' => 'Cashout'
            ],
            [
                'notification_type_name' => 'Order Voucher'
            ],
            [
                'notification_type_name' => 'Support Ticket'
            ],
        ]);
    }
}
