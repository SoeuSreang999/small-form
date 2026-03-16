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
        if (!Schema::hasTable('questions_response')) {
            Schema::create('questions_response', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('form_response_id')->nullable();
                $table->unsignedBigInteger('question_id')->nullable();
                $table->double('score')->nullable();
                $table->unsignedBigInteger('moderator_id')->nullable();
                $table->dateTime('moderated_on')->nullable();
                $table->longText('moderator_comment')->nullable();
                $table->smallInteger('is_auto_correct')->nullable();
                $table->smallInteger('is_correct')->nullable();
                $table->longText('response_text')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('deleted_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();

                $table->unique(['form_response_id', 'question_id'], 'questions_response_form_question_unique');
                $table->index('form_response_id', 'questions_response_form_response_idx');
                $table->index('question_id', 'questions_response_question_idx');
            });
        }

        if (!Schema::hasTable('question_response_inputs')) {
            Schema::create('question_response_inputs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('questions_response_id')->nullable();
                $table->string('answer_type')->nullable();
                $table->string('matching_type', 10)->nullable();
                $table->integer('option_id')->nullable();
                $table->integer('prompt_id')->nullable();
                $table->integer('answer_id')->nullable();
                $table->integer('index')->nullable();
                $table->integer('order')->nullable();
                $table->longText('text_response')->nullable();
                $table->string('name_en')->nullable();
                $table->longText('link')->nullable();
                $table->longText('iframe_link')->nullable();
                $table->longText('thumbnail')->nullable();
                $table->string('file_type')->nullable();
                $table->double('duration')->nullable();
                $table->longText('file_path')->nullable();
                $table->double('file_size')->nullable();
                $table->longText('attachment')->nullable();
                $table->double('score')->nullable();
                $table->smallInteger('is_correct')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('deleted_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();

                $table->index('questions_response_id', 'question_response_inputs_parent_idx');
                $table->index('option_id', 'question_response_inputs_option_idx');
                $table->index('answer_id', 'question_response_inputs_answer_idx');
                $table->index('prompt_id', 'question_response_inputs_prompt_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_response_inputs');
        Schema::dropIfExists('questions_response');
    }
};
