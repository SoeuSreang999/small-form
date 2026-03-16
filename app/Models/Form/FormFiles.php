<?php

namespace App\Models\Form;

use App\Models\Files;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormFiles extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo(FormSections::class, 'section_id', 'id');
    }

    public function scopeForQuestion($query, int $questionId)
    {
        return $query->where('question_id', $questionId);
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId)->whereNull('question_id');
    }

    public static function createForQuestion(int $questionId, int $fileId, ?int $sectionId = null): self
    {
        return static::create([
            'file_id'       => $fileId,
            'question_id'   => $questionId,
            'section_id'    => $sectionId,
        ]);
    }

    public static function createForSection(int $sectionId, int $fileId): self
    {
        return static::create([
            'file_id'       => $fileId,
            'section_id'    => $sectionId,
            'question_id'   => null,
        ]);
    }
}
