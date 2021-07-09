<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserCartAddProductIdQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->dropColumn('products_data');
            $table->integer('quantity')->after('user_id')->default(1);
            $table->integer('checked')->after('quantity')->default(0);
            $table->unsignedBigInteger('product_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->dropColumn('product_id');
            $table->dropColumn('quantity');
            $table->dropColumn('checked');
            $table->longText('products_data')->after('user_id');
        });
    }
}
