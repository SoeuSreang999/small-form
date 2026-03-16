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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->integer('type')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('month')->nullable();
            $table->integer('assign_status')->nullable();
            $table->datetime('publish_date')->nullable();
            $table->datetime('publish_result_date')->nullable();

            $table->string('header')->nullable();
            $table->string('footer')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_image')->nullable();
            $table->string('description')->nullable();
            
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
        Schema::dropIfExists('forms');
    }
};
