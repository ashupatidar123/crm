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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('branch_code',150)->nullable();
            $table->string('branch_name',150)->nullable();
            $table->string('country',120)->nullable();
            $table->string('address',150)->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email',120)->nullable();
            $table->string('website_url',120)->nullable();
            $table->string('gst_no',120)->nullable();
            $table->string('branch_logo',250)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('description',250)->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('company_id')
              ->references('id')
              ->on('companies')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
