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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name',150);
            $table->string('currency',150);
            $table->string('address',150);
            $table->integer('zip_code');
            $table->string('phone',20);
            $table->string('fax',120);
            $table->string('email',120);
            $table->string('website_url',120);
            $table->string('gst_no',120);
            $table->string('company_logo',250);
            $table->tinyInteger('is_active')->default(1);
            $table->string('description',250)->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
