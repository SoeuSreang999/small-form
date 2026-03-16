<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Model;

class FormTypes extends Model
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
}
