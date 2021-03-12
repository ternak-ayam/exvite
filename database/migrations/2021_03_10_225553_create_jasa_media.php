<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJasaMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jasa_product_media', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('jasa_id')->unsigned();
            $table->text('small');
            $table->text('medium');
            $table->text('large');
            $table->timestamps();
            
            $table->foreign('jasa_id')->references('jasa_id')->on('jasa_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jasa_media');
    }
}
