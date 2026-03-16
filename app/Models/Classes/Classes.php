<?php

namespace App\Models\Classes;

use App\Models\User;
use App\Models\Classes\Subjects;
use Illuminate\Support\Facades\App;
use App\Models\Classes\ClassSubjects;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function userCreatedBy()
    {
       return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function classSubjects()
    {
       return $this->hasMany(ClassSubjects::class, 'class_id');
    }
}
