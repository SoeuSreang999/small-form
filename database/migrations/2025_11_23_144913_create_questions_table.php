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
        Schema::create('questions', function (Blueprint $table) {
            $table->id('id');
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->boolean('assign_point')->default(false);
            $table->integer('type')->nullable();
            $table->decimal('point', 6, 2)->nullable();
            $table->text('content')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('option_shuffle')->default(false);
            $table->boolean('limit_word')->default(false);
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->boolean('validate_text')->default(false);
            $table->text('short_answer')->nullable();
            $table->integer('image')->nullable();
            $table->string('desc_en')->nullable();
            $table->string('desc_kh')->nullable();

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
        Schema::dropIfExists('questions');
    }
};
