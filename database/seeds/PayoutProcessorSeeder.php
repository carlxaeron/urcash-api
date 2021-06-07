<?php

use Illuminate\Database\Seeder;
use App\PayoutProcessor;

class PayoutProcessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PayoutProcessor::create([
            'proc_id' => 'AUB',
            'description' => 'Asia United Bank CA/SA'
        ]);

        PayoutProcessor::create([
            'proc_id' => 'BDO',
            'description' => 'Banco de Oro CA/SA'
        ]);

        PayoutProcessor::create([
            'proc_id' => 'BPI',
            'description' => 'BPI CA/SA'
        ]);

        PayoutProcessor::create([
            'proc_id' => 'BFB',
            'description' => 'BPI Family Bank'
        ]);

        PayoutProcessor::create([
            'proc_id' => 'CBC',
            'description' => 'Chinabank CA/SA'
        ]);

        PayoutProcessor::create([
             'proc_id' => 'EWB',
             'description' => 'EastWest CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'LBP',
             'description' => 'Landbank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'MBTC',
             'description' => 'Metrobank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PNB',
             'description' => 'PNB individual CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'RCBC',
             'description' => 'RCBC CA/SA, RCBC Savings Bank CA/SA, RCBC MyWallet'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'RSB',
             'description' => 'Robinsons Bank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'SBC',
             'description' => 'Security Bank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'UBP',
             'description' => 'Unionbank CA/SA, EON'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'SBC',
             'description' => 'Security Bank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'UCPB',
             'description' => 'UCPB CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'SBC',
             'description' => 'Security Bank CA/SA'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'CEBL',
             'description' => 'Cebuana Lhuillier Cash Pick-up'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'LBC',
             'description' => 'LBC Cash Pick-up'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PLWN',
             'description' => 'Palawan Pawnshop Cash Pick-up (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PRHB',
             'description' => 'PRHB PeraHub Cash Pick-up'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'RCBP',
             'description' => 'RCBP RCBC/RCBC Savings Bank Cash Pick-up (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'RDP',
             'description' => 'RD Pawnshop Cash Pickup (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'TRMY',
             'description' => 'TrueMoney Cash Pick-up (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'BITC',
             'description' => 'Coins.ph Wallet (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'GCSH',
             'description' => 'Gcash'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'SMRT',
             'description' => 'Smart Money (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'MAY',
             'description' => 'Maybank'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'SBA',
             'description' => 'Sterling Bank of Asia'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'DBP',
             'description' => 'Development Bank of the Philippines (reserved)'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PBCM',
             'description' => 'Philippine Bank of Communications'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PSB',
             'description' => 'Philippine Savings Bank'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PVB',
             'description' => 'Philippine Veterans Bank'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'BOC',
             'description' => 'Bank of Commerce'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'CBCS',
             'description' => 'Chinabank Savings Bank'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'CTBC',
             'description' => 'Chinatrust'
         ]);

         PayoutProcessor::create([
             'proc_id' => 'PYMY',
             'description' => 'Smart PayMaya'
         ]);
    }
}
