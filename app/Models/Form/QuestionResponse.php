<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    protected $table = 'questions_response';

    protected $guarded = ['id'];

    protected $casts = [
        'moderated_on' => 'datetime',
        'score' => 'float',
    ];

    public function formResponse()
    {
        return $this->belongsTo(FormResponse::class, 'form_response_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }

    public function inputs()
    {
        return $this->hasMany(\App\Models\Form\QuestionResponseInput::class, 'questions_response_id', 'id')
            ->orderByRaw('COALESCE(`order`, 999999), COALESCE(`index`, 999999), `id`');
    }
}
