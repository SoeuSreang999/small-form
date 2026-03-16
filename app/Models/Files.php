<?php

namespace App\Models;

use App\Models\Form\FormFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Files extends Model
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

    public function isType()
    {
        return ($this->type);
    }

    public function isDocument()
    {
        return $this->isType('document');
    }

    public function isPDF()
    {
        return $this->isType('pdf');
    }

    public function isSpreadsheet()
    {
        return $this->isType('spreadsheet');
    }

    public function isPresentation()
    {
        return $this->isType('presentation');
    }

    public function isTextContent()
    {
        return $this->isType('text-content');
    }

    public function isVideo()
    {
        return $this->isType('video');
    }

    public function isImage()
    {
        return in_array($this->type, $this->imageArray());
    }

    public function isAudio()
    {
        $arry_type = [
            'mp3',
            'wav',
            'ogg',
            'm4a',
            'webm',
            'flac',
            'aac'
        ];

        return in_array($this->type, $arry_type);
    }

    public function imageArray()
    {
        return [
            'gif',
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'webp',
            'tiff',
            'svg'
        ];
    }

    public function formFiles()
    {
        return $this->hasMany(FormFiles::class, 'file_id', 'id')->orderBy('files.id', 'DESC');
    }

    public function getThumbnailImageAttribute()
    {
        return $this->thumbnail ? asset(Storage::url($this->thumbnail)) : null;
    }
    
    public function getFilePathAttribute()
    {
        return $this->path ? asset(Storage::url($this->path)) : null;
    }
}
