<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseItemsAddBatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->enum('payment_method',config('purchase_statuses.payment_method.v1'))->after('price');
            $table->enum('purchase_status',config('purchase_statuses.purchase_status.v1'))->after('price');
            $table->enum('status',config('purchase_statuses.status.v1'))->after('price');
            $table->uuid('batch_code')->after('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('purchase_status');
            $table->dropColumn('status');
            $table->dropColumn('batch_code');
        });
    }
}
