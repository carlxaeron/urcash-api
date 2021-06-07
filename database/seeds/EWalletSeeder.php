<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\Utils\GenerateRandomIntegers;

class EWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('e_wallets')->delete();

        DB::table('e_wallets')->insert([
            [
                'name' => 'Coins.PH',
            ],
            [
                'name' => 'GCash',
            ],
            [
                'name' => 'Paymaya',
            ],
        ]);
    }
}
