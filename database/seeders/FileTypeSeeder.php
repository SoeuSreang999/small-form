<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('file_types')->insert([
            // Documents
            ['name' => 'PDF Document', 'slug' => 'pdf', 'mime_type' => 'application/pdf', 'extension' => 'pdf', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Word Document', 'slug' => 'doc', 'mime_type' => 'application/msword', 'extension' => 'doc', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Word Document (DOCX)', 'slug' => 'docx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'extension' => 'docx', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Excel Spreadsheet', 'slug' => 'xls', 'mime_type' => 'application/vnd.ms-excel', 'extension' => 'xls', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Excel Spreadsheet (XLSX)', 'slug' => 'xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'extension' => 'xlsx', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PowerPoint', 'slug' => 'ppt', 'mime_type' => 'application/vnd.ms-powerpoint', 'extension' => 'ppt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PowerPoint (PPTX)', 'slug' => 'pptx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'extension' => 'pptx', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Text File', 'slug' => 'txt', 'mime_type' => 'text/plain', 'extension' => 'txt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'CSV File', 'slug' => 'csv', 'mime_type' => 'text/csv', 'extension' => 'csv', 'created_at' => $now, 'updated_at' => $now],

            // Images
            ['name' => 'JPEG Image', 'slug' => 'jpg', 'mime_type' => 'image/jpeg', 'extension' => 'jpg,jpeg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PNG Image', 'slug' => 'png', 'mime_type' => 'image/png', 'extension' => 'png', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'GIF Image', 'slug' => 'gif', 'mime_type' => 'image/gif', 'extension' => 'gif', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SVG Image', 'slug' => 'svg', 'mime_type' => 'image/svg+xml', 'extension' => 'svg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'WebP Image', 'slug' => 'webp', 'mime_type' => 'image/webp', 'extension' => 'webp', 'created_at' => $now, 'updated_at' => $now],

            // Audio
            ['name' => 'MP3 Audio', 'slug' => 'mp3', 'mime_type' => 'audio/mpeg', 'extension' => 'mp3', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'WAV Audio', 'slug' => 'wav', 'mime_type' => 'audio/wav', 'extension' => 'wav', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'OGG Audio', 'slug' => 'ogg', 'mime_type' => 'audio/ogg', 'extension' => 'ogg', 'created_at' => $now, 'updated_at' => $now],

            // Video
            ['name' => 'MP4 Video', 'slug' => 'mp4', 'mime_type' => 'video/mp4', 'extension' => 'mp4', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'AVI Video', 'slug' => 'avi', 'mime_type' => 'video/x-msvideo', 'extension' => 'avi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'MOV Video', 'slug' => 'mov', 'mime_type' => 'video/quicktime', 'extension' => 'mov', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'MKV Video', 'slug' => 'mkv', 'mime_type' => 'video/x-matroska', 'extension' => 'mkv', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'WebM Video', 'slug' => 'webm', 'mime_type' => 'video/webm', 'extension' => 'webm', 'created_at' => $now, 'updated_at' => $now],

            // Archives
            ['name' => 'ZIP Archive', 'slug' => 'zip', 'mime_type' => 'application/zip', 'extension' => 'zip', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'RAR Archive', 'slug' => 'rar', 'mime_type' => 'application/vnd.rar', 'extension' => 'rar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '7-Zip Archive', 'slug' => '7z', 'mime_type' => 'application/x-7z-compressed', 'extension' => '7z', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'TAR Archive', 'slug' => 'tar', 'mime_type' => 'application/x-tar', 'extension' => 'tar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'GZIP Archive', 'slug' => 'gz', 'mime_type' => 'application/gzip', 'extension' => 'gz', 'created_at' => $now, 'updated_at' => $now],

            // Code / Data
            ['name' => 'JSON File', 'slug' => 'json', 'mime_type' => 'application/json', 'extension' => 'json', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'XML File', 'slug' => 'xml', 'mime_type' => 'application/xml', 'extension' => 'xml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HTML File', 'slug' => 'html', 'mime_type' => 'text/html', 'extension' => 'html', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'CSS File', 'slug' => 'css', 'mime_type' => 'text/css', 'extension' => 'css', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'JavaScript File', 'slug' => 'js', 'mime_type' => 'application/javascript', 'extension' => 'js', 'created_at' => $now, 'updated_at' => $now],

            // Fonts
            ['name' => 'TrueType Font', 'slug' => 'ttf', 'mime_type' => 'font/ttf', 'extension' => 'ttf', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'OpenType Font', 'slug' => 'otf', 'mime_type' => 'font/otf', 'extension' => 'otf', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Web Open Font', 'slug' => 'woff', 'mime_type' => 'font/woff', 'extension' => 'woff', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Web Open Font 2', 'slug' => 'woff2', 'mime_type' => 'font/woff2', 'extension' => 'woff2', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
