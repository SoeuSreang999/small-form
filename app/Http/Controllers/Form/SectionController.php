<?php

namespace App\Http\Controllers\Form;

use App\Models\Files;
use Laravel\Pail\File;
use Illuminate\Http\Request;
use App\Models\Form\FormFiles;
use App\Models\Form\Forms;
use App\Models\Form\FormSections;
use App\Models\Form\FormQuestions;
use App\Models\Form\Questions;
use App\Models\Form\QuestionOptions;
use App\Models\Form\QuestionAnswerTypes;
use App\Models\Form\QuestionSettings;
use App\Models\Form\QuestionSpecificFileSettings;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function copy(Request $request, FormSections $section)
    {
        $validated = $request->validate([
            'form_id' => ['required', 'integer'],
        ]);

        $formId = (int) $validated['form_id'];

        if ((int) $section->form_id !== $formId) {
            abort(422, 'Section does not belong to this form.');
        }

        try {
            $datas = DB::transaction(function () use ($formId, $section) {
                $insertOrder = ((int) ($section->order ?? 0)) + 1;

                FormSections::where('form_id', $formId)
                    ->whereNotNull('order')
                    ->where('order', '>=', $insertOrder)
                    ->increment('order');

                $newSection = FormSections::create([
                    'form_id'     => $formId,
                    'name_en'     => $section->name_en,
                    'name_kh'     => $section->name_kh,
                    'desc_en'     => $section->desc_en,
                    'desc_kh'     => $section->desc_kh,
                    'order'       => $insertOrder,
                    'created_by'  => Auth::id(),
                    'updated_by'  => Auth::id(),
                ]);

                $sectionFiles = FormFiles::query()
                    ->where('section_id', $section->id)
                    ->whereNull('question_id')
                    ->get();
                foreach ($sectionFiles as $sf) {
                    FormFiles::create([
                        'file_id'     => $sf->file_id,
                        'section_id'  => $newSection->id,
                        'question_id' => null,
                        'created_by'  => Auth::id(),
                        'updated_by'  => Auth::id(),
                    ]);
                }

                $formQuestions = FormQuestions::query()
                    ->where('form_section_id', $section->id)
                    ->orderBy('order', 'ASC')
                    ->get();

                $newQuestions = collect();

                foreach ($formQuestions as $index => $fq) {
                    $originalQuestion = Questions::withTrashed()->find($fq->question_id);
                    if (!$originalQuestion) {
                        continue;
                    }

                    $isMatching = $originalQuestion->isMatching();

                    $newQuestion = $originalQuestion->replicate();
                    $newQuestion->created_by = Auth::id();
                    $newQuestion->updated_by = Auth::id();
                    $newQuestion->deleted_by = null;
                    $newQuestion->deleted_at = null;
                    $newQuestion->save();

                    FormQuestions::create([
                        'form_id'           => $formId,
                        'form_section_id'   => $newSection->id,
                        'question_id'       => $newQuestion->id,
                        'order'             => $index + 1,
                    ]);

                    $originalSetting = QuestionSettings::where('question_id', $originalQuestion->id)->first();
                    if ($originalSetting) {
                        $newSetting = QuestionSettings::create([
                            'question_id'    => $newQuestion->id,
                            'specific_file'  => $originalSetting->specific_file,
                            'max_file'       => $originalSetting->max_file,
                            'max_size'       => $originalSetting->max_size,
                            'is_matching'    => $originalSetting->is_matching ?? false,
                            'created_by'     => Auth::id(),
                            'updated_by'     => Auth::id(),
                        ]);

                        $specifics = QuestionSpecificFileSettings::where('question_setting_id', $originalSetting->id)->get();
                        foreach ($specifics as $spec) {
                            QuestionSpecificFileSettings::create([
                                'question_setting_id'        => $newSetting->id,
                                'question_specific_file_id'  => $spec->question_specific_file_id,
                                'created_by'                 => Auth::id(),
                                'updated_by'                 => Auth::id(),
                            ]);
                        }
                    }

                    if ($isMatching) {
                        $sentences = QuestionOptions::where('question_id', $originalQuestion->id)
                            ->whereNull('sentence_id')
                            ->get();

                        foreach ($sentences as $sentence) {
                            $answer = $sentence->answer_id
                                ? QuestionOptions::where('question_id', $originalQuestion->id)->where('id', $sentence->answer_id)->first()
                                : null;

                            $newSentence = QuestionOptions::create([
                                'name_en'      => $sentence->name_en,
                                'name_kh'      => $sentence->name_kh,
                                'question_id'  => $newQuestion->id,
                                'index'        => $sentence->index,
                                'option_type'  => $sentence->option_type,
                                'is_correct'   => $sentence->is_correct,
                                'score'        => $sentence->score,
                                'image'        => $sentence->image,
                                'des_en'       => $sentence->des_en,
                                'des_kh'       => $sentence->des_kh,
                                'created_by'   => Auth::id(),
                                'updated_by'   => Auth::id(),
                            ]);

                            $newAnswer = null;
                            if ($answer) {
                                $newAnswer = QuestionOptions::create([
                                    'name_en'      => $answer->name_en,
                                    'name_kh'      => $answer->name_kh,
                                    'question_id'  => $newQuestion->id,
                                    'index'        => $answer->index,
                                    'option_type'  => $answer->option_type,
                                    'is_correct'   => $answer->is_correct,
                                    'score'        => $answer->score,
                                    'image'        => $answer->image,
                                    'des_en'       => $answer->des_en,
                                    'des_kh'       => $answer->des_kh,
                                    'sentence_id'  => $newSentence->id,
                                    'created_by'   => Auth::id(),
                                    'updated_by'   => Auth::id(),
                                ]);
                            }

                            $newSentence->answer_id = $newAnswer ? $newAnswer->id : null;
                            $newSentence->save();
                        }
                    } else {
                        $options = QuestionOptions::where('question_id', $originalQuestion->id)->get();
                        foreach ($options as $option) {
                            QuestionOptions::create([
                                'name_en'      => $option->name_en,
                                'name_kh'      => $option->name_kh,
                                'question_id'  => $newQuestion->id,
                                'index'        => $option->index,
                                'option_type'  => $option->option_type,
                                'is_correct'   => $option->is_correct,
                                'score'        => $option->score,
                                'image'        => $option->image,
                                'des_en'       => $option->des_en,
                                'des_kh'       => $option->des_kh,
                                'created_by'   => Auth::id(),
                                'updated_by'   => Auth::id(),
                            ]);
                        }
                    }

                    $formFiles = FormFiles::where('question_id', $originalQuestion->id)->get();
                    foreach ($formFiles as $ff) {
                        FormFiles::create([
                            'file_id'     => $ff->file_id,
                            'section_id'  => $newSection->id,
                            'question_id' => $newQuestion->id,
                            'created_by'  => Auth::id(),
                            'updated_by'  => Auth::id(),
                        ]);
                    }

                    $newQuestion->form_id = $formId;
                    $newQuestion->section_id = $newSection->id;
                    $newQuestions->push($newQuestion);
                }

                return [
                    'section'     => $newSection,
                    'secInd'      => null,
                    'qans_types'  => QuestionAnswerTypes::get()->pluck('name', 'id'),
                    'questions'   => $newQuestions,
                ];
            });

            return view('forms.sections.list-section-item', $datas);
        } catch (\Throwable $th) {
            $status = $th instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                ? (int) $th->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'error'   => $th->getMessage(),
            ], $status);
        }
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'form_id'    => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'question_action' => ['nullable', 'in:move,delete'],
            'destination_section_id' => ['nullable', 'integer'],
        ]);

        $formId = (int) $validated['form_id'];
        $sectionId = (int) $validated['section_id'];
        $questionAction = $validated['question_action'] ?? null;
        $destinationSectionId = isset($validated['destination_section_id']) ? (int) $validated['destination_section_id'] : null;

        try {
            DB::transaction(function () use ($formId, $sectionId, $questionAction, $destinationSectionId) {
                $form = Forms::query()->where('id', $formId)->first();
                if (!$form) {
                    abort(404, 'Form not found.');
                }

                $section = FormSections::query()
                    ->where('form_id', $formId)
                    ->where('id', $sectionId)
                    ->first();

                if (!$section) {
                    abort(404, 'Section not found.');
                }

                $totalSections = (int) FormSections::query()->where('form_id', $formId)->count();
                if ($totalSections <= 1) {
                    abort(422, 'Cannot delete the only section.');
                }

                $movingFormQuestions = FormQuestions::query()
                    ->where('form_section_id', $sectionId)
                    ->orderBy('order', 'ASC')
                    ->get();

                $movingQuestionIds = $movingFormQuestions->pluck('question_id')->toArray();

                if ($questionAction === 'delete') {
                    foreach ($movingFormQuestions as $fq) {
                        $fq->deleted_by = Auth::id();
                        $fq->updated_by = Auth::id();
                        $fq->save();
                        $fq->delete();

                        if (!$form->isAssign()) {
                            $question = Questions::query()->where('id', $fq->question_id)->first();
                            if ($question) {
                                $question->deleted_by = Auth::id();
                                $question->updated_by = Auth::id();
                                $question->save();
                                $question->delete();
                            }
                        }
                    }
                } else {
                    // Default behavior: move questions away from deleted section
                    $destination = null;

                    if ($questionAction === 'move' && $destinationSectionId) {
                        $destination = FormSections::query()
                            ->where('form_id', $formId)
                            ->where('id', $destinationSectionId)
                            ->where('id', '!=', $sectionId)
                            ->first();
                    }

                    if (!$destination) {
                        $destination = FormSections::query()
                            ->where('form_id', $formId)
                            ->where('id', '!=', $sectionId)
                            ->orderBy('order', 'ASC')
                            ->first();
                    }

                    if (!$destination) {
                        abort(422, 'No destination section found.');
                    }

                    $destinationMaxOrder = (int) FormQuestions::query()
                        ->where('form_section_id', $destination->id)
                        ->max('order');

                    foreach ($movingFormQuestions as $idx => $fq) {
                        $fq->form_id = $formId;
                        $fq->form_section_id = $destination->id;
                        $fq->order = $destinationMaxOrder + $idx + 1;
                        $fq->updated_by = Auth::id();
                        $fq->save();
                    }

                    if (!empty($movingQuestionIds)) {
                        FormFiles::query()
                            ->whereIn('question_id', $movingQuestionIds)
                            ->update([
                                'section_id' => $destination->id,
                                'updated_by' => Auth::id(),
                            ]);
                    }
                }

                $section->deleted_by = Auth::id();
                $section->updated_by = Auth::id();
                $section->save();
                $section->delete();

                $remaining = FormSections::query()
                    ->where('form_id', $formId)
                    ->orderBy('order', 'ASC')
                    ->get();
                foreach ($remaining as $idx => $sec) {
                    $sec->order = $idx + 1;
                    $sec->updated_by = Auth::id();
                    $sec->save();
                }
            });

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            $status = $th instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                ? (int) $th->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'error'   => $th->getMessage(),
            ], $status);
        }
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'form_id'        => ['required', 'integer'],
            'section_ids'    => ['required', 'array', 'min:1'],
            'section_ids.*'  => ['required', 'integer'],
        ]);

        $formId = (int) $validated['form_id'];
        $sectionIds = array_values(array_unique(array_map('intval', $validated['section_ids'])));

        try {
            DB::transaction(function () use ($formId, $sectionIds) {
                $sections = FormSections::query()
                    ->where('form_id', $formId)
                    ->whereIn('id', $sectionIds)
                    ->get()
                    ->keyBy('id');

                if ($sections->count() !== count($sectionIds)) {
                    abort(422, 'Some sections do not belong to this form.');
                }

                foreach ($sectionIds as $index => $sectionId) {
                    $sec = $sections->get($sectionId);
                    $sec->order = $index + 1;
                    $sec->updated_by = Auth::id();
                    $sec->save();
                }
            });

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            $status = $th instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                ? (int) $th->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'error'   => $th->getMessage(),
            ], $status);
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'form_id'          => ['required', 'integer'],
            'after_section_id' => ['nullable', 'integer'],
        ]);

        try {
            $datas = DB::transaction(function () use ($validated) {
                $formId = (int) $validated['form_id'];
                $afterSectionId = isset($validated['after_section_id']) ? (int) $validated['after_section_id'] : null;

                $afterSection = null;
                if ($afterSectionId) {
                    $afterSection = FormSections::where('form_id', $formId)->where('id', $afterSectionId)->first();
                }

                $maxOrder = (int) FormSections::where('form_id', $formId)->max('order');
                $insertOrder = $maxOrder + 1;

                if ($afterSection && $afterSection->order !== null) {
                    $insertOrder = ((int) $afterSection->order) + 1;
                    FormSections::where('form_id', $formId)
                        ->whereNotNull('order')
                        ->where('order', '>=', $insertOrder)
                        ->increment('order');
                }

                $section = FormSections::create([
                    'form_id'     => $formId,
                    'name_en'     => 'Untitled',
                    'order'       => $insertOrder,
                    'created_by'  => Auth::id(),
                    'updated_by'  => Auth::id(),
                ]);

                $question = Questions::create([
                    'name_en'     => null,
                    'type'        => 1,
                    'created_by'  => Auth::id(),
                    'updated_by'  => Auth::id(),
                ]);

                FormQuestions::create([
                    'form_id'           => $formId,
                    'form_section_id'   => $section->id,
                    'question_id'       => $question->id,
                    'order'             => 1,
                ]);

                QuestionOptions::create([
                    'question_id' => $question->id,
                    'name_en'     => 'Option 1',
                    'option_type' => $question->option_type,
                    'is_correct'  => 0,
                ]);

                $question->form_id = $formId;
                $question->section_id = $section->id;

                return [
                    'section'     => $section,
                    'secInd'      => null,
                    'qans_types'  => QuestionAnswerTypes::get()->pluck('name', 'id'),
                    'question'    => $question,
                ];
            });

            return view('forms.sections.list-section-item', $datas);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, FormSections $section){
        try {
            DB::transaction(function () use ($request, $section) {
                $section->update([
                    'name_en'       => $request->name_en,
                    'desc_en'       => $request->desc_en,
                    'updated_by'    => Auth::id(),
                ]);
            });
            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ], 500);
        }
    }

    public function voiceRecord(Request $request){
        try {
            $datas = DB::transaction(function () use ($request) {
                $sectionId          = $request->section_id??0;
                $section            = FormSections::find($sectionId);
                $voice              = $request->file('voice');
                $folder             =  date('Ymd').'/'.Auth::id();
                $document_html      = '';

                if($voice){
                    $filename   = date('YmdHis').'.'.$voice->extension();
                    $filePath   = Storage::disk('public')->putFileAs(
                        $folder,
                        $voice,
                        $filename,
                    );
                    $resFile = fileStoreData([
                        'name_en'       => $filename,
                        'path'          => $filePath,
                        'type'          => $voice->extension(),
                        'size'          => $voice->getSize(),
                    ]);
                    FormFiles::createForSection($sectionId, $resFile->id);

                    $file = $section?->files()
                        ->where('files.id', $resFile->id)
                        ->first();
                    $document_html .= view('forms.files.view.file', compact('file'))->render();
                }
                return [
                    'document_html' => $document_html,
                ];
            });
            return response()->json(array_merge([
                'success' => true,
            ], $datas));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sectionBrowseFile(Request $request, FormSections $section)
    {
        $histories = Files::query()
            ->where('created_by', Auth::id())
            ->whereNotNull('path')
            ->latest('id')
            ->limit(30)
            ->get();

        $activeTab = $request->query('tab', 'history');

        return view('forms.sections.contents.files.index', compact('section', 'histories', 'activeTab'));
    }

    public function sectionBrowseFilePC(Request $request, FormSections $section)
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'max:10240'],
            ]);

            $datas = DB::transaction(function () use ($request, $section) {
                $upload = $request->file('file');
                $folder = date('Ymd') . '/' . Auth::id();
                $filename = date('YmdHis') . '_' . uniqid() . '.' . $upload->extension();

                $filePath = Storage::disk('public')->putFileAs(
                    $folder,
                    $upload,
                    $filename
                );

                $resFile = fileStoreData([
                    'name_en' => $filename,
                    'path'    => $filePath,
                    'type'    => $upload->extension(),
                    'size'    => $upload->getSize(),
                    'created_by' => Auth::id(),
                ]);

                FormFiles::createForSection($section->id, $resFile->id);

                $file = $section->files()
                    ->where('files.id', $resFile->id)
                    ->first();

                return [
                    'document_html' => $file ? view('forms.files.view.file', compact('file'))->render() : '',
                ];
            });

            return response()->json(array_merge([
                'success' => true,
            ], $datas));
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ], 500);
        }
    }

    public function sectionAttachHistoryFile(Request $request, FormSections $section)
    {
        try {
            $request->validate([
                'file_id' => ['required', 'integer', 'exists:files,id'],
            ]);

            $datas = DB::transaction(function () use ($request, $section) {
                $fileId = (int) $request->file_id;

                $exists = FormFiles::query()
                    ->forSection($section->id)
                    ->where('file_id', $fileId)
                    ->exists();

                if (!$exists) {
                    FormFiles::createForSection($section->id, $fileId);
                }

                $file = $section->files()
                    ->where('files.id', $fileId)
                    ->first();

                return [
                    'document_html' => $file ? view('forms.files.view.file', compact('file'))->render() : '',
                    'exists' => $exists,
                ];
            });

            return response()->json(array_merge([
                'success' => true,
            ], $datas));
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
            ], 500);
        }
    }
}
