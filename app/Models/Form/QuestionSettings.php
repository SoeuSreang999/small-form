<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionSettings extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function isMatching(): bool
    {
        return $this->is_matching??0 === 1;
    }
}
