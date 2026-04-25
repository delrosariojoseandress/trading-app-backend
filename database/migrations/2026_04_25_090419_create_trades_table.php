<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('buy_order_id');
            $table->unsignedBigInteger('sell_order_id');

            $table->decimal('price', 18, 8);
            $table->decimal('quantity', 18, 8);
            $table->decimal('fee', 18, 8)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trades');
    }
}