<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_account_transaction_id');
            $table->integer('payment_method_id');
            $table->integer('voucher_id');
            $table->string('transaction_description')->nullable();
            $table->integer('number_of_vouchers');
            $table->float('amount',8,2);
            $table->float('fee');
            $table->string('proof_of_payment')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_orders');
    }
}
