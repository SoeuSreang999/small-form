<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormResponse extends Model
{
    use SoftDeletes;

    protected $table = 'form_response';

    protected $guarded = ['id'];
    protected $casts = [
        'starting' => 'datetime',
        'finished' => 'datetime',
    ];

    public function questionResponses()
    {
        return $this->hasMany(\App\Models\Form\QuestionResponse::class, 'form_response_id', 'id');
    }
}
