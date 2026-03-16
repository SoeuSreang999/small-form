<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Files;
use Illuminate\Support\HtmlString;

class QuestionOptions extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $appends = ['name', 'label_name'];

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'kh') {
            return $this->name_kh ?? $this->name_en;
        }
        return $this->name_en ?? $this->name_kh;
    }

    public function answerMatching()
    {
        return $this->belongsTo(QuestionOptions::class, 'answer_id', 'id');
    }

    public function optionImage()
    {
        return $this->hasOne(Files::class, 'id', 'image');
    }

    public function optionElImage()
    {
        $option     = $this;
        $image      = $this->optionImage;
        if ($image) {
            $view   = view('forms.questions.contents.options.image', compact('image','option'))->render();
            return new HtmlString($view);
        }
        return null;
    }

    public function getLabelNameAttribute()
    {
        $locale         = app()->getLocale();
        $questionId     = $this->question_id;
        $optionId       = $this->id;
        $for            = 'question' . $questionId . '_option' . $optionId;

        $index          = $this->option_index ?? $this->index;
        $index          = $locale === 'kh' ? numberToKhmerLetters($index) : numberToEnLetters($index);
        $indexPrefix    = $index !== null ? '<span class="me-1">' . e($index) . '.</span>' : '';

        $html = '<label class="form-check-label text-body fs-5" for="' . e($for) . '">'
            . $indexPrefix
            . e($this->name ?? '')
            . '</label>';

        return new HtmlString($html);
    }
}
