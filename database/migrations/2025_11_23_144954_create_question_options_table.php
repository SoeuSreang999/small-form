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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id('id');
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->integer('question_id');
            $table->integer('index')->nullable();
            $table->string('option_type');
            $table->boolean('is_correct')->default(0);
            $table->decimal('score', 6, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('des_en')->nullable();
            $table->string('des_kh')->nullable();

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
        Schema::dropIfExists('question_options');
    }
};
