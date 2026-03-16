<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionSpecificFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_specific_files')->insert([
            [
                'id' => 1,
                'name_en' => 'Image',
                'name_kh' => 'រូបភាព',
                'slug' => 'image',
                'mime_type' => 'image/*',
                'extension' => 'jpg,jpeg,png,gif,webp',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name_en' => 'Video',
                'name_kh' => 'វីដេអូ',
                'slug' => 'video',
                'mime_type' => 'video/*',
                'extension' => 'mp4,avi,mov,mkv',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'name_en' => 'PDF',
                'name_kh' => 'ឯកសារ PDF',
                'slug' => 'pdf',
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'name_en' => 'Document',
                'name_kh' => 'ឯកសារ',
                'slug' => 'document',
                'mime_type' => 'application/*',
                'extension' => 'doc,docx,xls,xlsx,ppt,pptx,txt,csv',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'name_en' => 'Audio',
                'name_kh' => 'សម្លេង',
                'slug' => 'audio',
                'mime_type' => 'audio/*',
                'extension' => 'mp3,wav,ogg,aac',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'name_en' => 'Draw',
                'name_kh' => 'គំនូរ',
                'slug' => 'draw',
                'mime_type' => 'image/*',
                'extension' => 'svg,ai,psd,xd,fig,sketch',
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => Carbon::parse('2026-01-11 01:27:35'),
                'updated_at' => Carbon::parse('2026-01-11 01:27:35'),
                'deleted_at' => null,
            ],
        ]);
    }
}
