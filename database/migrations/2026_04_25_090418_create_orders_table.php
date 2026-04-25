<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            $table->enum('type', ['buy', 'sell']);

            $table->decimal('price', 18, 8);
            $table->decimal('quantity', 18, 8);
            $table->decimal('filled', 18, 8)->default(0);

            $table->enum('status', ['open', 'partial', 'filled', 'cancelled'])
                  ->default('open');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}