<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('subtotal', 10,2);
            $table->double('shipping', 10,2);
            $table->integer('coupon_code_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->double('discount', 10,2)->nullable();
            $table->double('grand_total', 10,2);
            //User Address related Columns
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile');
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->string('address');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
