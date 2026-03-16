<?php

namespace App\Models\Form;

use App\Models\User;
use App\Models\Form\FormSections;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forms extends Model
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

    public function userCreatedBy()
    {
       return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function scopeByCreator($query, $userId = null)
    {
        return $query->where('created_by', $userId ?? Auth::id());
    }

    public function sections()
    {
         return $this->hasMany(FormSections::class, 'form_id')->orderBy('order', 'ASC');
    }

    public function isAssign()
    {
       return ($this->assign_status == null OR $this->assign_status == 0)?false:true;
    }

    public function isImmediateResult(): bool
    {
        return $this->publish_result_date == null ? true : false;
    }
}
