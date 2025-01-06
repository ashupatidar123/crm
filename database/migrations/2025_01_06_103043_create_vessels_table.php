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
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_name',50)->nullable();
            $table->string('technical_manager',50)->nullable();
            $table->string('registered_owner',50)->nullable();
            $table->string('master',50)->nullable();
            $table->string('vesel_email',150)->nullable();
            $table->string('imo_no',50)->nullable();
            $table->string('category',80)->nullable();
            $table->string('type',120)->nullable();
            $table->string('delivery_date',50)->nullable();
            $table->string('dead_weight',50)->nullable();
            $table->string('main_engine',50)->nullable();
            $table->string('bhp',50)->nullable();
            $table->string('flag',50)->nullable();
            $table->string('grt',50)->nullable();
            $table->string('nrt',50)->nullable();
            $table->string('cy_number',50)->nullable();
            $table->string('de_number',50)->nullable();
            $table->string('sg_number',50)->nullable();
            $table->string('yard',50)->nullable();
            $table->string('sid',50)->nullable();
            $table->string('vessel_image',150)->nullable();
            $table->tinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('vessels');
    }
};
