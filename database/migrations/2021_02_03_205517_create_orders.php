<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jasa_orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('customer_id')->unsigned();
            $table->string('type');
            $table->string('note');
            $table->dateTime('deadline');
            $table->dateTime('batal_otomatis');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();

            // $table->foreign('product_id')->references('jasa_id')->on('jasa_products')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jasa_orders');
    }
}
