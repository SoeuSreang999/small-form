<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Subjects extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function userCreatedBy()
    {
       return $this->hasOne(User::class, 'id', 'created_by');
    }
}
