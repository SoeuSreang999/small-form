<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Form\SectionController;
use App\Http\Controllers\Form\QuestionController;

Route::middleware('auth')->group(function () {
    Route::prefix('forms')->as('forms.')->group(function () {
        Route::get('/list', [FormController::class, 'list'])->name('list');
        Route::get('/create', [FormController::class, 'create'])->name('create');
        Route::get('/{form:uuid?}', [FormController::class, 'index'])->whereUuid('form')->name('index');
        Route::post('/store', [FormController::class, 'store'])->name('store');
        Route::post('/edit/{id}', [FormController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [FormController::class, 'destroy'])->name('destroy');

        // public
        Route::prefix('public')->as('public.')->controller(FormController::class)->group(function () {
            Route::post('form/{form:uuid}', 'publicForm')->name('index');
        });

        // settings
        Route::prefix('settings')->as('settings.')->controller(FormController::class)->group(function () {
            Route::post('update/{form:uuid}', 'updateSettings')->name('update');
        });
        // Section
        Route::prefix('sections')->as('sections.')->controller(SectionController::class)->group(function () {
            Route::post('create', 'create')->name('create');
            Route::post('{section}', 'update')->whereNumber('section')->name('update');
            Route::post('reorder', 'reorder')->name('reorder');
            Route::post('copy/{section}', 'copy')->whereNumber('section')->name('copy');
            Route::delete('delete', 'destroy')->name('destroy');

            // files
            Route::prefix('files')->as('files.')->group(function () {
                Route::get('browse/{section}', 'sectionBrowseFile')->whereNumber('section')->name('browse');
                Route::post('upload-pc/{section}', 'sectionBrowseFilePC')->whereNumber('section')->name('upload.pc');
                Route::post('attach-history/{section}', 'sectionAttachHistoryFile')->whereNumber('section')->name('attach.history');
            });

            // voice record
            Route::prefix('voices')->as('voices.')->group(function () {
                Route::post('/', 'voiceRecord')->name('store');
            });
        });

        // question
        Route::prefix('questions')->as('questions.')->controller(QuestionController::class)->group(function () {
            Route::post('/create', 'create')->name('create');
            Route::post('/copy/{question}', 'copyQuestion')->name('copy');
            Route::delete('/delete', 'destroy')->name('destroy');
            Route::post('/update/{question}', 'update')->name('update');
            Route::post('/change-type/{question}', 'changeType')->name('change.type');
            Route::post('/reorder', 'reorder')->name('reorder');
            Route::post('/reorder-cross', 'reorderCrossSection')->name('reorder.cross');

            // voice record
            Route::prefix('voices')->as('voices.')->group(function () {
                Route::get('list/{question}', 'listRecord')->name('index');
                Route::get('create/{question}', 'voiceRecord')->name('create');
                Route::post('store/{question}', 'storeVoiceRecord')->name('store');
            });

            // file
            Route::prefix('images')->as('images.')->group(function () {
                Route::get('browse/{question}', 'questionBrowseImage')->name('browse');
                Route::post('upload/{question}', 'questionUploadImage')->name('upload');
                Route::delete('delete/{question}', 'questionDeleteImage')->name('remove');
            });

            // options
            Route::prefix('options')->as('options.')->group(function () {
                Route::post('add/{question}', 'addOption')->name('add');
                Route::delete('remove/{question}', 'removeOption')->name('remove');
                Route::get('browse-image/{option}', 'optionBrowseImage')->name('browse.image');
                Route::post('upload-image/{option}', 'optionUploadImage')->name('upload.image');
                Route::delete('delete-image/{option}', 'optionDeleteImage')->name('remove.image');
            });
        });

        // File
        Route::prefix('files')->as('files.')->controller(FormController::class)->group(function () {
            Route::delete('delete/{file}', 'formDeleteFile')->name('destroy');
        });

        Route::prefix('preview')->as('preview.')->controller(FormController::class)->group(function () {
            Route::get('/{form:uuid}', 'preview')->whereUuid('form')->name('index');
        });

        // Exam
        Route::prefix('exam')->as('exam.')->group(function () {
            Route::get('/{form:uuid}', [QuestionController::class, 'userExam'])->whereUuid('form')->name('index');

            // Question response
            Route::prefix('question')->as('question.')->controller(QuestionController::class)->group(function () {
                Route::post('/{question}', 'saveUserQuestionResponse')->whereNumber('question')->name('store');
            });
        });
    });
});
