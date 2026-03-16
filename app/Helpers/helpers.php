<?php

use App\Models\Files;
use App\Models\Folders;

if (!function_exists('fileUpload')) {
    function fileStoreData($file) {
        $folder = Folders::updateOrCreate(
            ['name_en' => date('d-m-Y')],
            ['name_en' => date('d-m-Y')]
        );
        $file['folder_id'] = $folder->id;
        $file       = Files::create($file);
        $thumbnail  = imageThumbnail($file->path);
        if ($thumbnail) {
            $file->thumbnail = $thumbnail;
            $file->save();
        }
        return $file;
    }
}


if (!function_exists('imageThumbnail')) {
    function imageThumbnail($filePath, $targetHeight = 100)
    {
        $filePath = storage_path('app/public/' . $filePath);

        if (!file_exists($filePath)) {
            return false;
        }

        $destinationDir     = dirname($filePath);
        $destinationPath    = $destinationDir . '/thumb_' . basename($filePath);

        if (!file_exists($destinationDir)) {
            if (!mkdir($destinationDir, 0775, true)) {
                error_log("Failed to create directory: $destinationDir");
                return false;
            }
        }

        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            error_log("Failed to get image info: $filePath");
            return false;
        }

        $mimeType       = $imageInfo['mime'];
        $originalWidth  = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        $ratio          = $originalHeight / $targetHeight;
        $targetWidth    = $originalWidth / $ratio;

        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filePath);
                break;
            default:
                error_log("Unsupported MIME type: $mimeType");
                return false;
        }

        if (!$sourceImage) {
            error_log("Failed to create source image from: $filePath");
            return false;
        }

        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        $result = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $result = imagejpeg($thumbnail, $destinationPath, 90);
                break;
            case 'image/png':
                $result = imagepng($thumbnail, $destinationPath, 9);
                break;
            case 'image/gif':
                $result = imagegif($thumbnail, $destinationPath);
                break;
        }

        if (!$result) {
            error_log("Failed to save thumbnail: $destinationPath");
        }

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return $result ? str_replace(storage_path('app/public/'), '', $destinationPath) : false;
    }

    if (!function_exists('numberToRomanNumber')) {
        function numberToRomanNumber($number)
        {
            $romanDigits = [
                '1' => 'I',
                '2' => 'II',
                '3' => 'III',
                '4' => 'IV',
                '5' => 'V',
                '6' => 'VI',
                '7' => 'VII',
                '8' => 'VIII',
                '9' => 'IX',
                '10' => 'X',
                '11' => 'XI',
            ];

            $numberString   = strval($number);
            $romanString    = '';

            for ($i = 0; $i < strlen($numberString); $i++) {
                $digit = $numberString[$i];
                $romanString .= $romanDigits[$digit] ?? $digit;
            }

            return $romanString;
        }
    }

    if (!function_exists('numberToKhNumber')) {
        function numberToKhNumber($number)
        {
            $khmerDigits = [
                '0' => '០',
                '1' => '១',
                '2' => '២',
                '3' => '៣',
                '4' => '៤',
                '5' => '៥',
                '6' => '៦',
                '7' => '៧',
                '8' => '៨',
                '9' => '៩'
            ];

            $numberString   = strval($number);
            $khmerString    = '';

            for ($i = 0; $i < strlen($numberString); $i++) {
                $digit = $numberString[$i];
                $khmerString .= $khmerDigits[$digit] ?? $digit;
            }

            return $khmerString;
        }
    }

    if (!function_exists('numberToKhmerLetters')) {
        function numberToKhmerLetters($number)
        {
            $khmerDigits = [
                '1' => 'ក',
                '2' => 'ខ',
                '3' => 'គ',
                '4' => 'ឃ',
                '5' => 'ង',
                '6' => 'ច',
                '7' => 'ឆ',
                '8' => 'ជ',
                '9' => 'ញ',
                '10' => 'ដ',
                '11' => 'ឋ',
                '12' => 'ឌ',
                '13' => 'ឍ',
                '14' => 'ណ',
                '15' => 'ត',
                '16' => 'ថ',
                '17' => 'ទ',
                '18' => 'ធ',
                '19' => 'ន',
                '20' => 'ប',
                '21' => 'ផ',
                '22' => 'ព',
                '23' => 'ភ',
                '24' => 'ម',
                '25' => 'យ',
                '26' => 'រ',
                '27' => 'ល',
                '28' => 'វ',
                '29' => 'ស',
                '30' => 'ហ',
                '31' => 'ឡ',
                '32' => 'អ',
            ];

            $numberString   = strval($number);
            $khmerString    = '';

            for ($i = 0; $i < strlen($numberString); $i++) {
                $digit = $numberString[$i];
                $khmerString .= $khmerDigits[$digit] ?? $digit;
            }

            return $khmerString;
        }
    }

    if (!function_exists('numberToEnLetters')) {
        function numberToEnLetters($number)
        {
            $number = (int) $number;
            if ($number <= 0) {
                return '';
            }

            $result = '';
            while ($number > 0) {
                $number--;
                $result = chr(65 + ($number % 26)) . $result;
                $number = intdiv($number, 26);
            }

            return $result;
        }
    }

    if (!function_exists('defaultDate')) {
        function defaultDate($date)
        {
           return $date != null ? date('Y-m-d H:i:s', strtotime($date)) : null;
        }
    }

    if (!function_exists('formDate')) {
        function formDate($date)
        {
           return $date != null ? date('D, d-m-Y H:i:s', strtotime($date)) : null;
        }
    }
}