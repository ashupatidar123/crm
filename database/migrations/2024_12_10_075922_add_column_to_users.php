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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('middle_name', 25)->nullable();
            $table->string('last_name', 25)->nullable();
            $table->string('login_id', 150)->nullable();
            $table->string('date_birth', 25)->nullable();
            $table->string('address')->nullable();
            $table->string('state', 50)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('phone', 13)->nullable();
            $table->string('phone1', 13)->nullable();
            $table->string('user_image',150)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->text('2fa_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role_id', 'department_id', 'middle_name', 'last_name', 'login_id',
                'date_birth', 'address', 'state', 'district', 'city', 'zipcode',
                'phone', 'phone1', 'user_image', 'is_active', '2fa_secret',
            ]);
        });
    }
};
