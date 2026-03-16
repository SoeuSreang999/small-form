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
        Schema::create('question_answer_types', function (Blueprint $table) {
            $table->id();
            
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('desc_en')->nullable();
            $table->string('desc_kh')->nullable();
            $table->string('input')->nullable();
            $table->string('view')->nullable();
            $table->string('slug')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answer_types');
    }
};
