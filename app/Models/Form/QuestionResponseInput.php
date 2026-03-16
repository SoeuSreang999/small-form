<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;

class QuestionResponseInput extends Model
{
    protected $table = 'question_response_inputs';

    protected $guarded = ['id'];

    protected $casts = [
        'duration' => 'float',
        'file_size' => 'float',
        'score' => 'float',
    ];

    public function questionResponse()
    {
        return $this->belongsTo(\App\Models\Form\QuestionResponse::class, 'questions_response_id', 'id');
    }
}
