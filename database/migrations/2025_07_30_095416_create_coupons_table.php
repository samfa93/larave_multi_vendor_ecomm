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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->double('value');
            $table->boolean('is_percent');
            $table->double('minimum_spend');
            $table->double('maximum_spend');
            $table->integer('usage_limit_per_coupon');
            $table->integer('usage_limit_per_customer');
            $table->integer('used')->default(0);
            $table->boolean('is_active')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
