<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\Utils\GenerateRandomIntegers;

class BanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->delete();

        DB::table('banks')->insert([
            [
                'name' => 'Banco de Oro UniBank',
            ],
            [
                'name' => 'BPI',
            ],
            [
                'name' => 'PNB',
            ],
        ]);
    }
}
