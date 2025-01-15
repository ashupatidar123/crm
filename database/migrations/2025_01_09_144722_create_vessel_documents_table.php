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
        Schema::create('vessel_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vessel_id');
            $table->unsignedBigInteger('document_id');
            $table->string('vessel_document',150);
            $table->string('description',250)->nullable();;
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        

            $table->foreign('vessel_id')
              ->references('id')
              ->on('vessels')
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
        Schema::dropIfExists('vessel_documents');
    }
};
