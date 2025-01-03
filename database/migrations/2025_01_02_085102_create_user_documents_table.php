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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('document_id');
            $table->string('document_type',50);
            $table->string('user_document',150);
            $table->string('description',250)->nullable();;
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        

            $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');

            $table->foreign('document_id')
              ->references('id')
              ->on('documents')
              ->onDelete('cascade'); 
        });       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
