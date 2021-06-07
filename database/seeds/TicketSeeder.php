<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\Utils\GenerateRandomIntegers;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tickets')->delete();
        // Initialize GenerateRandomIntegers
        $qrCode = new GenerateRandomIntegers(1, 9, 12);

        DB::table('tickets')->insert([
            [
                'qr_code' => $qrCode->generate(),
                'title' => 'B2P Ticket 1',
                'description' => 'B2P Ticket',
                'amount' => 100,
                'status' => 1,
            ],
            [
                'qr_code' => $qrCode->generate(),
                'title' => 'B2P Ticket 2',
                'description' => 'B2P Ticket',
                'amount' => 500,
                'status' => 1,
            ],
            [
                'qr_code' => $qrCode->generate(),
                'title' => 'B2P Ticket 3',
                'description' => 'B2P Ticket',
                'amount' => 1000,
                'status' => 1,
            ],
        ]);
    }
}
