<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionSpecificFileSettings extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
}
