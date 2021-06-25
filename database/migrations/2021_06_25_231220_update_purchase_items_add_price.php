<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseItemsAddPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('product_id');
            $table->foreign('user_id')->references('id')->on('users'); // Foreign key: Products model
            $table->double('price',20,2)->after('quantity');
            $table->dropForeign('purchase_items_purchase_id_foreign');
            $table->dropColumn('purchase_id');
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
            $table->dropColumn('price');
            $table->dropForeign('purchase_items_user_id_foreign');
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('purchase_id')->after('id');
            $table->foreign('purchase_id')->references('id')->on('purchases'); // Foreign key: Purchases model
        });
    }
}
