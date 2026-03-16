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
        Schema::create('form_response', function (Blueprint $table) {
            $table->id();
            $table->integer('form_id');
            $table->integer('user_id');
            $table->datetime('starting')->nullable();
            $table->datetime('finished')->nullable();
            $table->integer('remaining_time')->nullable();
            $table->integer('is_submitted')->nullable();
            $table->integer('all_correct')->nullable();
            
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
        Schema::dropIfExists('form_resporns');
    }
};
