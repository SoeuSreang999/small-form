<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormQuestions extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function options(){
        return array(1,2,3);
    }
}
