<?php

use Illuminate\Database\Seeder;
use App\PaymentMethod;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->delete();
        PaymentMethod::create([
            'title' => 'GCash',
            'description' => 'GCash payment method',
            'account_name' => 'UCC CORP' ,
            'account_number' => '09171234567',
            'status' => true
        ]);
        PaymentMethod::create([
            'title' => 'Coins.ph',
            'description' => 'Coins.ph payment method',
            'account_name' => 'UCC CORP',
            'account_number' => '09171234567',
            'status' => true
        ]);
        PaymentMethod::create([
            'title' => 'Paymaya',
            'description' => 'Paymaya payment method',
            'account_name' => 'UCC CORP',
            'account_number' => '09171234567',
            'status' => true
        ]);
        PaymentMethod::create([
            'title' => 'BDO',
            'description' => 'Banco de Oro',
            'account_name' => 'UCC CORP',
            'account_number' => '123412341234',
            'status' => true
        ]);
    }
}
