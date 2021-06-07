<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vouchers')->delete();
        DB::table('vouchers')->insert([
            [
                'title' => 'Voucher 1',
                'description' => 'Voucher 1 description',
                'amount' => 100,
                'status' => true
            ],
            [
                'title' => 'Voucher 2',
                'description' => 'Voucher 2 description',
                'amount' => 100,
                'status' => true
            ],
            [
                'title' => 'Voucher 3',
                'description' => 'Voucher 3 description',
                'amount' => 100,
                'status' => true
            ],
        ]);
    }
}
