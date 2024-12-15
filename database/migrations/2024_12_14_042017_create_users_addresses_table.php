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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('country',150);
            $table->string('state',150);
            $table->string('city',150);
            $table->string('phone1',20);
            $table->string('phone2',20);
            $table->integer('zip_code');
            $table->string('address1',250);
            $table->string('address2',250);
            $table->string('address3',250);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_addresses');
    }
};
