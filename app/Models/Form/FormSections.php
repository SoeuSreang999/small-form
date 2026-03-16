<?php

namespace App\Models\Form;

use App\Models\Files;
use App\Models\Form\FormQuestions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSections extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'kh') {
            return $this->name_kh ?? $this->name_en;
        }
        return $this->name_en ?? $this->name_kh;
    }

    public function questions()
    {
         return $this->hasManyThrough(
            Questions::class,
            FormQuestions::class,
            'form_section_id',
            'id',
            'id',
            'question_id'
        )
        ->select(
            'questions.*',
            'form_questions.form_id',
            'form_questions.form_section_id as section_id',
            'form_questions.order as order'
        )
        ->orderBy('form_questions.order', 'ASC');
    }


    public function files()
    {
        return $this->hasManyThrough(
            Files::class,
            FormFiles::class,
            'section_id',
            'id',
            'id',
            'file_id'
        )
        ->whereNull('form_files.question_id')
        ->select('files.*', 'form_files.section_id', 'form_files.question_id');
    }

    public function formFiles()
    {
        return $this->hasMany(FormFiles::class, 'section_id', 'id');
    }
}
