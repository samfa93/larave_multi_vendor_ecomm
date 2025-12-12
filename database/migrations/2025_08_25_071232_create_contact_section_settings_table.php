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
        Schema::create('contact_section_settings', function (Blueprint $table) {
            $table->id();
            $table->text('map_url')->nullable();
            
            $table->string('title_one')->nullable();
            $table->string('address_one')->nullable();
            $table->string('phone_one')->nullable();
            $table->string('email_one')->nullable();

            $table->string('title_two')->nullable();
            $table->string('address_two')->nullable();
            $table->string('phone_two')->nullable();
            $table->string('email_two')->nullable();

            $table->string('title_three')->nullable();
            $table->string('address_three')->nullable();
            $table->string('phone_three')->nullable();
            $table->string('email_three')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_section_settings');
    }
};
