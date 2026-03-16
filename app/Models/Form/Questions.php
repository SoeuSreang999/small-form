<?php

namespace App\Models\Form;

use App\Models\Files;
use App\Models\FileTypes;
use Illuminate\Support\HtmlString;
use App\Models\Form\QuestionSettings;
use Illuminate\Database\Eloquent\Model;
use App\Models\Form\QuestionSpecificFiles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questions extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'kh') {
            return $this->name_kh ?? $this->name_en;
        }
        return $this->name_en ?? $this->name_kh;
    }

    public function files()
    {
        return $this->hasManyThrough(
            Files::class,
            FormFiles::class,
            'question_id',
            'id',
            'id',
            'file_id'
        )->addSelect(
            'files.*',
            'form_files.section_id',
            'form_files.question_id',
        );
    }

    public function formFiles()
    {
        return $this->hasMany(FormFiles::class, 'question_id', 'id');
    }

    public function getPointAttribute($value)
    {
        return $value !== null ? number_format($value, 0) : null;
    }

    public function answerType()
    {
        return $this->hasOne(QuestionAnswerTypes::class, 'id', 'type');
    }

    public function options()
    {
        return $this->hasMany(QuestionOptions::class, 'question_id', 'id')->where(function($query) {
            if ($this->isMatching()) {
                $query->whereNull('sentence_id');
            }
        });
    }

    public function answerSentences()
    {
        if ($this->isMatching()) {
            return $this->hasMany(QuestionOptions::class, 'question_id', 'id')->whereNotNull('sentence_id');
        }
        return collect([]);
    }

    public function getOptionTypeAttribute()
    {
        $input = $this->answerType?->input;
        return $input ?: 'input';
    }

    public function getNameLabelAttribute()
    {
        $index      = $this->question_index ?? null;

        $locale     = app()->getLocale();
        $qIndex     =  $locale === 'kh' ? numberToKhNumber($index) : $index;
        $name       = $locale === 'kh'
            ? ($this->name_kh ?? $this->name_en)
            : ($this->name_en ?? $this->name_kh);
        $name      = $name ?: __('general.not_title');

        $requiredEl         = $this->isRequired() ? '<span class="text-danger">*</span>' : '';
        $assignPointEl      = $this->isAssignPoint()
            ? '<span class="">(' . $this->point . ' points)</span>'
            : '';

        return new HtmlString(trim($qIndex . '. ' . $name . ' ' . $assignPointEl . ' ' . $requiredEl));
    }

    public function isScq(): bool
    {
        return $this->answerType?->slug === 'scq';
    }

    public function isMcq(): bool
    {
        return $this->answerType?->slug === 'mcq';
    }

    public function isShortText(): bool
    {
        return $this->answerType?->slug === 'short-text';
    }

    public function isWriting(): bool
    {
        return $this->answerType?->slug === 'writing';
    }

    public function isTrueFalse(): bool
    {
        return $this->answerType?->slug === 'true-false';
    }

    public function isFileUpload(): bool
    {
        return $this->answerType?->slug === 'file-upload';
    }

    public function isGapFilling(): bool
    {
        return $this->answerType?->slug === 'gap-filling';
    }

    public function isMatching(): bool
    {
        return $this->answerType?->slug === 'matching';
    }

    public function isOrdering(): bool
    {
        return $this->answerType?->slug === 'ordering';
    }

    function isInstruction(): bool
    {
        return $this->question_type === 'instruction';
    }

    function isRequired(): bool
    {
        return $this->required === 1;
    }

    public function isShuffle(): bool
    {
        return $this->option_shuffle === 1;
    }

    public function isAssignPoint(): bool
    {
        return $this->assign_point === 1;
    }

    public function fileTypes()
    {
        return QuestionSpecificFiles::get();
    }

    public function QuestionSetting()
    {
        return $this->hasOne(QuestionSettings::class, 'question_id', 'id');
    }

    public function questionSpecificFileSettings()
    {
        return $this->hasManyThrough(
            QuestionSpecificFileSettings::class,
            QuestionSettings::class,
            'question_id',
            'question_setting_id',
            'id',
            'id'
        );
    }

    public function content()
    {
        $this->refresh();
        $type       = $this->answerType;
        $slug       = $type->slug??null;
        $viewPath   = 'forms.questions.contents.'.$slug;
        $view       = '';

        if (!view()->exists($viewPath)) {
            return new HtmlString('');
        }

        if ($this->isScq() OR $this->isMcq() OR $this->isOrdering() OR $this->isTrueFalse()) {
            foreach ($this->options as $opInd => $option) {
                $view .= view($viewPath, [
                    'option'   => $option,
                    'question' => $this,
                    'opInd'    => $opInd,
                ])->render();
            }
        }elseif ($this->isWriting()) {
            $view .= view($viewPath, [
                'question' => $this,
            ])->render();
        }elseif($this->isMatching()) {
            $options    = $this->options->whereNull('sentence_id');
            foreach ($options as $opInd => $option) {
                $view .= view($viewPath, [
                    'option'   => $option,
                    'question' => $this,
                    'opInd'    => $opInd,
                ])->render();
            }
        }else {
            $view .= view($viewPath, [
                'question'       => $this,
                'fileTypes'     => $this->fileTypes()
            ])->render();
        }

        return new HtmlString($view);
    }

    public function questionImage()
    {
        return $this->hasOne(Files::class, 'id', 'image');
    }

    public function questionElImage()
    {
        $question     = $this;
        $image        = $this->questionImage;
        if ($image) {
            $view   = view('forms.questions.contents.images.image', compact('image','question'))->render();
            return new HtmlString($view);
        }
        return null;
    }

    public function questionFiles()
    {
        $view = '';

        foreach ($this->files as $file) {
            $view .= view('forms.files.view.file', compact('file'))->render();
        }

        return new HtmlString($view);
    }

    public static function formatGapPreviewInputTag(array $match): string
    {
        $attrs = $match[1] ?? '';

        if (!preg_match('/\bclass\s*=\s*"[^"]*\bform-control\b[^"]*"/i', $attrs)) {
            if (preg_match('/\bclass\s*=\s*"([^"]*)"/i', $attrs, $classMatch)) {
                $existing = trim($classMatch[1]);
                $replacement = 'class="' . trim($existing . ' form-control d-inline-block gapfilling-input text-center') . '"';
                $attrs = preg_replace('/\bclass\s*=\s*"[^"]*"/i', $replacement, $attrs);
            } else {
                $attrs .= ' class="form-control d-inline-block gapfilling-input text-center"';
            }
        }

        if (!preg_match('/\bstyle\s*=\s*"/i', $attrs)) {
            $attrs .= ' style="width:auto; min-width:120px; display:inline-block;"';
        }

        if (!preg_match('/\bvalue\s*=\s*"/i', $attrs)) {
            $attrs .= ' value=""';
        }

        return '<input ' . trim($attrs) . '>';
    }

    public function previewContent()
    {
        $this->refresh();
        $type       = $this->answerType;
        $slug       = $type->slug??null;
        $viewPath   = 'forms.views.questions.contents.'.$slug;
        $view       = '';

        if (!view()->exists($viewPath)) {
            return new HtmlString('');
        }

        if ($this->isScq() OR $this->isMcq() OR $this->isOrdering() OR $this->isTrueFalse()) {
            $options = $this->options()->when($this->isShuffle(), fn ($q) => $q->inRandomOrder())->get();
            foreach ($options as $opInd => $option) {
                $view .= view($viewPath, [
                    'option'   => $option,
                    'question' => $this,
                    'opInd'    => $opInd,
                ])->render();
            }
        }elseif ($this->isWriting()) {
            $view .= view($viewPath, [
                'question' => $this,
            ])->render();
        }elseif($this->isMatching()) {
            $sentences  = $this->options()
                            ->whereNull('sentence_id')
                            ->when($this->isShuffle(), fn ($q) => $q->inRandomOrder())
                            ->get();
            $ansIds     = $sentences->pluck('answer_id')->toArray();
            $answers    = $this->answerSentences->whereIn('id', $ansIds)->shuffle()->values();
            $view .= view($viewPath, [
                'sentences'     => $sentences,
                'answers'       => $answers,
                'question'      => $this,
            ])->render();
        }elseif ($this->isGapFilling()) {
            $options = $this->options()->when($this->isShuffle(), fn ($q) => $q->inRandomOrder())->get();
            $view .= view($viewPath, [
                'question' => $this,
                'options'  => $options,
            ])->render();
        }else {
            $view .= view($viewPath, [
                'question'       => $this,
                'fileTypes'     => $this->fileTypes()
            ])->render();
        }

        return new HtmlString($view);
    }
}
