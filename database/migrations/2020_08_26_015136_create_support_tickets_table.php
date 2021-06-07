<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_to_user');
            $table->foreign('assigned_to_user')->references('id')->on('users'); // Foreign key: Users model
            $table->integer('reference_number');
            $table->string('name');
            $table->string('mobile_number')->nullable();
            $table->string('email');
            $table->string('issue');
            $table->string('priority')->default('Low');
            $table->longText('description');
            $table->boolean('is_resolved')->default(0);
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
        Schema::dropIfExists('support_tickets');
    }
}
