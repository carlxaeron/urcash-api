<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->nullable();
            $table->string('txn_id')->nullable();
            $table->string('refNo')->nullable();
            $table->integer('wallet_id')->nullable();
            $table->string('transaction_description')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('amount')->nullable();
            $table->decimal('charge_amount')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('wallet_transactions');
    }
}
