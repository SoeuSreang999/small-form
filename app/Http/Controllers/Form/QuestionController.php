<?php

namespace App\Http\Controllers\Form;

use Livewire\Form;
use App\Models\Form\Forms;
use Illuminate\Http\Request;
use App\Models\Form\FormFiles;
use App\Models\Form\FormResponse;
use App\Models\Form\QuestionResponse;
use App\Models\Form\Questions;
use App\Models\Form\QuestionResponseInput;
use App\Models\Form\FormSections;
use App\Models\Form\FormQuestions;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Form\QuestionOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Form\QuestionAnswerTypes;
use App\Models\Form\QuestionSettings;
use App\Models\Form\QuestionSpecificFileSettings;

class QuestionController extends Controller
{
    protected $question_types;

    public function __construct()
    {
        $this->question_types = QuestionAnswerTypes::get()->pluck('name','id');
    }

    public function create(Request $request){
       try {
            $datas = DB::transaction(function () use($request) {
                $form_id            = $request->form_id ?? null;
                $section_id         = $request->section_id ?? null;
                $after_question_id  = $request->after_question_id ?? null;
                $question_type      = $request->input('question_type', 'question');
                $question_type      = in_array($question_type, ['instruction', 'question'], true) ? $question_type : 'question';

                $maxOrder = (int) FormQuestions::where([
                    'form_id' => $form_id,
                    'form_section_id' => $section_id,
                ])->max('order');

                $newOrder = $maxOrder + 1;
                if ($after_question_id) {
                    $after = FormQuestions::where([
                        'form_id' => $form_id,
                        'form_section_id' => $section_id,
                        'question_id' => $after_question_id,
                    ])->first();

                    if ($after && $after->order !== null) {
                        $newOrder = ((int) $after->order) + 1;
                        if ($newOrder <= $maxOrder) {
                            FormQuestions::where([
                                'form_id' => $form_id,
                                'form_section_id' => $section_id,
                            ])->where('order', '>=', $newOrder)->increment('order');
                        }
                    }
                }

                $question = Questions::create([
                    'name_en'         => null,
                    'type'            => $question_type === 'instruction' ? null : 1,
                    'question_type'   => $question_type,
                    'created_by'      => Auth::id(),
                ]);

                if ($question_type !== 'instruction') {
                    $this->changeType($request, $question);
                }
                FormQuestions::create([
                    'form_id'           => $form_id,
                    'form_section_id'   => $section_id,
                    'question_id'       => $question->id,
                    'order'             => $newOrder,
                ]);
                $question->form_id      = $form_id;
                $question->section_id   = $section_id;
                return [
                    'qans_types'    => $this->question_types,
                    'question'      => $question,
                ];
            });
            return view('forms.questions.question', $datas);
       } catch (\Throwable $th) {
            throw $th;
       }
    }

    public function copyQuestion(Request $request, Questions $question)
    {
        $validated = $request->validate([
            'form_id'    => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'after_question_id' => ['nullable', 'integer'],
        ]);

        try {
            $datas = DB::transaction(function () use ($validated, $question) {
                $formId     = (int) $validated['form_id'];
                $sectionId  = (int) $validated['section_id'];
                $afterQuestionId = isset($validated['after_question_id']) ? (int) $validated['after_question_id'] : null;

                $maxOrder = (int) FormQuestions::where([
                    'form_id' => $formId,
                    'form_section_id' => $sectionId,
                ])->max('order');

                $newOrder = $maxOrder + 1;
                if ($afterQuestionId) {
                    $after = FormQuestions::where([
                        'form_id' => $formId,
                        'form_section_id' => $sectionId,
                        'question_id' => $afterQuestionId,
                    ])->first();
                    if ($after && $after->order !== null) {
                        $newOrder = ((int) $after->order) + 1;
                        if ($newOrder <= $maxOrder) {
                            FormQuestions::where([
                                'form_id' => $formId,
                                'form_section_id' => $sectionId,
                            ])->where('order', '>=', $newOrder)->increment('order');
                        }
                    }
                }

                $newQuestion = Questions::create([
                    'name_en'         => $question->name_en,
                    'name_kh'         => $question->name_kh,
                    'assign_point'    => $question->assign_point,
                    'type'            => $question->type,
                    'question_type'   => $question->question_type ?? 'question',
                    'point'           => $question->point,
                    'content'         => $question->content,
                    'required'        => $question->required,
                    'option_shuffle'  => $question->option_shuffle,
                    'limit_word'      => $question->limit_word,
                    'min'             => $question->min,
                    'max'             => $question->max,
                    'validate_text'   => $question->validate_text,
                    'short_answer'    => $question->short_answer,
                    'image'           => $question->image,
                    'desc_en'         => $question->desc_en,
                    'desc_kh'         => $question->desc_kh,
                    'created_by'      => Auth::id(),
                    'updated_by'      => Auth::id(),
                ]);

                FormQuestions::create([
                    'form_id'           => $formId,
                    'form_section_id'   => $sectionId,
                    'question_id'       => $newQuestion->id,
                    'order'             => $newOrder,
                ]);

                $isInstruction = (($question->question_type ?? 'question') === 'instruction');

                if (!$isInstruction) {
                    $originalSetting = QuestionSettings::where('question_id', $question->id)->first();
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

                    if ($question->isMatching()) {
                        $sentences = QuestionOptions::where('question_id', $question->id)
                            ->whereNull('sentence_id')
                            ->get();

                        foreach ($sentences as $sentence) {
                            $answer = $sentence->answer_id
                                ? QuestionOptions::where('question_id', $question->id)->where('id', $sentence->answer_id)->first()
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
                        $options = QuestionOptions::where('question_id', $question->id)->get();
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
                }

                $formFiles = FormFiles::where('question_id', $question->id)->get();
                foreach ($formFiles as $ff) {
                    FormFiles::create([
                        'file_id'     => $ff->file_id,
                        'section_id'  => $sectionId,
                        'question_id' => $newQuestion->id,
                        'created_by'  => Auth::id(),
                        'updated_by'  => Auth::id(),
                    ]);
                }

                $newQuestion->form_id       = $formId;
                $newQuestion->section_id    = $sectionId;

                return [
                    'qans_types' => $this->question_types,
                    'question'   => $newQuestion,
                ];
            });

            return view('forms.questions.question', $datas);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Questions $question){
        try {
            DB::transaction(function () use($request, $question) {
                $assign_point               = ($request->assign_point == 1)? true: false;
                $required                   = ($request->required == 1)? true: false;
                $option_shuffle             = ($request->option_shuffle == 1)? true: false;

                $question->assign_point     = $assign_point;
                $question->point            = $request->point;
                $question->type             = $request->type;
                $question->name_en          = $request->name_en;
                $question->desc_en          = $request->desc_en;
                $question->required         = $required;
                $question->option_shuffle   = $option_shuffle;
                $question->updated_by       = Auth::id();
                $question->save();
                $this->updateContent($request, $question);
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
                'line'      => $th->getLine()
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'form_id'                => ['required', 'integer'],
            'section_id'             => ['required', 'integer'],
            'question_ids'           => ['required', 'array', 'min:1'],
            'question_ids.*'         => ['required', 'integer'],
        ]);

        $formId         = (int) $validated['form_id'];
        $sectionId      = (int) $validated['section_id'];
        $questionIds    = array_values(array_unique(array_map('intval', $validated['question_ids'])));

        try {
            DB::transaction(function () use ($formId, $sectionId, $questionIds) {
                $section = FormSections::query()->where('id', $sectionId)->first();
                if (!$section || (int) $section->form_id !== $formId) {
                    abort(422, 'Section does not belong to this form.');
                }

                $formQuestions = FormQuestions::query()
                    ->where('form_section_id', $sectionId)
                    ->whereIn('question_id', $questionIds)
                    ->get()
                    ->keyBy('question_id');

                if ($formQuestions->count() !== count($questionIds)) {
                    abort(422, 'Some questions do not belong to this section.');
                }

                foreach ($questionIds as $index => $questionId) {
                    $fq                 = $formQuestions->get($questionId);
                    $fq->order          = $index + 1;
                    $fq->form_id        = $formId;
                    $fq->updated_by     = Auth::id();
                    $fq->save();
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

    public function reorderCrossSection(Request $request)
    {
        $validated = $request->validate([
            'form_id'                   => ['required', 'integer'],
            'from_section_id'           => ['required', 'integer'],
            'to_section_id'             => ['required', 'integer'],
            'source_question_ids'       => ['present', 'array'],
            'source_question_ids.*'     => ['integer'],
            'target_question_ids'       => ['required', 'array', 'min:1'],
            'target_question_ids.*'     => ['integer'],
        ]);

        $formId                 = (int) $validated['form_id'];
        $fromSectionId          = (int) $validated['from_section_id'];
        $toSectionId            = (int) $validated['to_section_id'];
        $sourceQuestionIds      = array_values(array_unique(array_map('intval', $validated['source_question_ids'])));
        $targetQuestionIds      = array_values(array_unique(array_map('intval', $validated['target_question_ids'])));

        if ($fromSectionId === $toSectionId) {
            return response()->json([
                'success' => true,
            ], 200);
        }

        $allQuestionIds = array_values(array_unique(array_merge($sourceQuestionIds, $targetQuestionIds)));

        try {
            DB::transaction(function () use ($formId, $fromSectionId, $toSectionId, $sourceQuestionIds, $targetQuestionIds, $allQuestionIds) {
                $fromSection = FormSections::query()->where('id', $fromSectionId)->first();
                $toSection = FormSections::query()->where('id', $toSectionId)->first();

                if (!$fromSection || !$toSection || (int) $fromSection->form_id !== $formId || (int) $toSection->form_id !== $formId) {
                    abort(422, 'Section does not belong to this form.');
                }

                $formQuestions = FormQuestions::query()
                    ->whereIn('form_section_id', [$fromSectionId, $toSectionId])
                    ->whereIn('question_id', $allQuestionIds)
                    ->get()
                    ->keyBy('question_id');

                if ($formQuestions->count() !== count($allQuestionIds)) {
                    abort(422, 'Some questions do not belong to these sections.');
                }

                foreach ($sourceQuestionIds as $index => $questionId) {
                    $fq = $formQuestions->get($questionId);
                    if (!$fq) {
                        continue;
                    }
                    $fq->form_id = $formId;
                    $fq->form_section_id = $fromSectionId;
                    $fq->order = $index + 1;
                    $fq->updated_by = Auth::id();
                    $fq->save();
                }

                foreach ($targetQuestionIds as $index => $questionId) {
                    $fq = $formQuestions->get($questionId);
                    if (!$fq) {
                        continue;
                    }
                    $fq->form_id = $formId;
                    $fq->form_section_id = $toSectionId;
                    $fq->order = $index + 1;
                    $fq->updated_by = Auth::id();
                    $fq->save();
                }

                if (!empty($sourceQuestionIds)) {
                    FormFiles::query()
                        ->whereIn('question_id', $sourceQuestionIds)
                        ->update([
                            'section_id' => $fromSectionId,
                            'updated_by' => Auth::id(),
                        ]);
                }
                if (!empty($targetQuestionIds)) {
                    FormFiles::query()
                        ->whereIn('question_id', $targetQuestionIds)
                        ->update([
                            'section_id' => $toSectionId,
                            'updated_by' => Auth::id(),
                        ]);
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

    function updateContent($request, $question){
        if (($question->question_type ?? 'question') === 'instruction') {
            return;
        }
        $questionId         = $question->id;
        $type               = $question->answerType;
        $slug               = $type->slug;
        $questionOptions    = new QuestionOptions();
        $QuestionSettings   = new QuestionSettings();
        $correctOptions     = $request->input("option_{$questionId}", []);

        $answers = $request->input('answers', []);
        if (!is_array($answers)) {
            $answers = [];
        }

        $sentences = $request->input('sentences', []);
        if (!is_array($sentences)) {
            $sentences = [];
        }

        if ($question->isScq() || $question->isMcq()) {
            foreach ($answers as $optionId => $ans) {
                $isCorrect = in_array((int) $optionId, array_map('intval', $correctOptions));
                $questionOptions->updateOrCreate(
                    [
                        'id'                => $optionId,
                        'question_id'       => $questionId,
                    ],
                    [
                        'name_en'           => $ans,
                        'is_correct'        => $isCorrect,
                    ]
                );
            }
        }elseif ($question->isWriting()) {
            $question->limit_word   = $request->input("limit_word", false);
            $question->min          = $request->input("min", null);
            $question->max          = $request->input("max", null);
            $question->save();
        }elseif ($question->isShortText()) {
            $question->validate_text    = $request->input("validate_text", false);
            $question->short_answer     = $request->input("short_answer", null);
            $question->save();
        }elseif ($question->isTrueFalse()) {
           foreach ($answers as $optionId => $ans) {
                $isCorrect = isset($correctOptions[$optionId]) ? $correctOptions[$optionId] : 0;
                $questionOptions->updateOrCreate(
                    [
                        'id'                => $optionId,
                        'question_id'       => $questionId,
                    ],
                    [
                        'name_en'           => $ans,
                        'is_correct'        => $isCorrect,
                    ]
                );
            }
        }elseif ($question->isFileUpload()) {
            $questionSpecificFileSettings   = new QuestionSpecificFileSettings();
            $specific_file                  = $request->input("specific_file", 0);
            $file_types                     = $request->input("file_types", []);

            $setting = $QuestionSettings->updateOrCreate(
                ['question_id' => $questionId],
                [
                    'specific_file' => $specific_file,
                    'max_file'      => $request->input("max_file", null),
                    'max_size'      => $request->input("max_size", null),
                    'created_by'    => Auth::id(),
                    'updated_by'    => Auth::id(),
                ]
            );

            $existing = $questionSpecificFileSettings::where('question_setting_id', $setting->id)->get();
            foreach ($existing as $ex) {
                if (!in_array($ex->question_specific_file_id, $file_types)) {
                    $ex->deleted_by = Auth::id();
                    $ex->save();
                    $ex->delete();
                }
            }

            foreach ($file_types as $type) {
                $record = $questionSpecificFileSettings::withTrashed()
                    ->where('question_setting_id', $setting->id)
                    ->where('question_specific_file_id', $type)
                    ->first();

                if ($record) {
                    if ($record->trashed()) {
                        $record->deleted_at  = null;
                        $record->deleted_by  = null;
                    }
                    $record->updated_by = Auth::id();
                    $record->save();
                } else {
                    $questionSpecificFileSettings::create([
                        'question_setting_id'       => $setting->id,
                        'question_specific_file_id' => $type,
                        'created_by'                => Auth::id(),
                        'updated_by'                => Auth::id(),
                    ]);
                }
            }
        }elseif ($question->isGapFilling()) {
            $contents               = $request->input("contents", null);
            $text                   = preg_replace(
                '/<input[^>]+value="([^"]*)"[^>]*>/',
                '$1',
                $contents
            );
            $question->content           = $contents;
            $question->short_answer      = $text;
            $question->save();

            $QuestionSettings->updateOrCreate(
                ['question_id' => $questionId],
                [
                    'is_matching'       => $request->input("is_matching", 0),
                    'created_by'        => Auth::id(),
                    'updated_by'        => Auth::id(),
                ]
            );

            preg_match_all(
                '/<input[^>]+name="gaps_(\d+)\[\]"[^>]+data-index="(\d+)"[^>]+value="([^"]*)"/',
                $contents,
                $matches
            );

            $questionIdFromHtml = $matches[1][0] ?? $question->id;
            $gaps   = array();
            foreach ($matches[2] as $i => $gapIndex) {
                $gaps[(int) $gapIndex] = $matches[3][$i];
            }
            ksort($gaps);

            $existingGaps   = $questionOptions::where('question_id', $questionId)->pluck('index')->toArray();
            $newGapIndices  = array_keys($gaps);
            $gapsToDelete   = array_diff($existingGaps, $newGapIndices);

            if (!empty($gapsToDelete)) {
                $questionOptions::where('question_id', $questionId)
                    ->whereIn('index', $gapsToDelete)
                    ->update([
                        'deleted_by' => Auth::id(),
                        'deleted_at' => now(),
                    ]);
            }

            foreach ($gaps as $index => $value) {
                $questionOptions->updateOrCreate(
                    [
                        'question_id'   => $questionId,
                        'index'         => $index,
                    ],
                    [
                        'name_en'       => $value,
                        'is_correct'    => 1,
                        'option_type'   => 'input',
                        'created_by'    => Auth::id(),
                        'updated_by'    => Auth::id(),
                    ]
                );
            }
        }elseif ($question->isMatching()) {
            foreach ($sentences as $optionId => $sentence) {
                $questionOptions->updateOrCreate(
                    [
                        'id'                => $optionId,
                        'question_id'       => $questionId,
                    ],
                    [
                        'name_en'           => $sentence,
                    ]
                );
            }

            foreach ($answers as $optionId => $answer) {
                $questionOptions->updateOrCreate(
                    [
                        'id'                => $optionId,
                        'question_id'       => $questionId,
                    ],
                    [
                        'name_en'           => $answer,
                    ]
                );
            }
        }elseif ($question->isOrdering()) {
            foreach ($answers as $optionId => $ans) {
                $questionOptions->updateOrCreate(
                    [
                        'id'                => $optionId,
                        'question_id'       => $questionId,
                    ],
                    [
                        'name_en'           => $ans,
                        'is_correct'        => 1,
                    ]
                );
            }
        }
    }

    public function destroy(Request $request){
       try {
            DB::transaction(function () use($request) {
                $form_id            = $request->form_id ?? null;
                $section_id         = $request->section_id ?? null;
                $question_id        = $request->question_id ?? null;

                $form               = Forms::find($form_id);
                $question           = Questions::find($question_id);
                $form_question      = FormQuestions::where([
                    'form_id'           => $form_id,
                    'form_section_id'   => $section_id,
                    'question_id'       => $question_id,
                ])->first();

                $form_question->update(['deleted_by' => Auth::id()]);
                $form_question->save();
                $form_question->delete();
                if(!$form->isAssign()){
                    $question->update(['deleted_by' => Auth::id()]);
                    $question->save();
                    $question->delete();
                }
            });
            return response()->json([
                'success'   => true,
                'title'     => __('messages.success'),
                'message'   => __('messages.success_delete'),
            ]);
       } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage()
            ], 500);
       }
    }

    public function voiceRecord(Request $request, Questions $question){
        return view('forms.voice.create',compact('request','question'));
    }

    function storeVoiceRecord(Request $request, Questions $question){
        try {
            $file_ids = [];
            DB::transaction(function () use ($request, $question, &$file_ids) {
                if ($request->hasFile('records')) {
                    foreach ($request->file('records') as $index => $record) {
                        $folder   = date('Ymd') . '/' . Auth::id();
                        $filename = date('YmdHis') . '_' .$index. '.' . $record->extension();
                        $filePath = Storage::disk('public')->putFileAs(
                            $folder,
                            $record,
                            $filename
                        );

                        $resFile = fileStoreData([
                            'name_en' => $filename,
                            'path'    => $filePath,
                            'type'    => $record->extension(),
                            'size'    => $record->getSize(),
                        ]);

                        FormFiles::createForQuestion(
                            $question->id,
                            $resFile->id,
                            $question->section_id
                        );
                        $file_ids[] = $resFile->id;
                    }
                }
            });

            return response()->json([
                'file_ids'  => $file_ids,
                'success'   => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage()
            ], 500);
        }
    }

    function listRecord(Request $request, Questions $question){
        try {
            $document_html  = '';
            $question_id    = $request->question_id ?? null;
            $file_ids       = $request->file_ids ?? [];
            $files = $question->files()
                ->whereIn('files.id', $file_ids)
                ->orderBy('files.id', 'DESC')
                ->get();
            foreach ($files as $file) {
                $document_html .= view('forms.files.view.file', compact('file','question'));
            }
            return $document_html;
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage()
            ], 500);
        }
    }

    public function addOption(Request $request, Questions $question){
        try {
            $view = DB::transaction(function () use($request, $question) {
                $optionType = $question->answerType;
                $viewPath   ='forms.questions.contents.'.$optionType->slug;
                $indexRow   = ($question->options->count() ?? 0) + 1;

                if ($question->isMatching()) {
                    $sentence           = QuestionOptions::create([
                        'name_en'       => 'Sentence '.$indexRow,
                        'question_id'   => $question->id,
                        'option_type'   => $question->option_type,
                        'is_correct'    => 1,
                    ]);
                    $answer             = QuestionOptions::create([
                        'name_en'       => 'Answer '.$indexRow,
                        'question_id'   => $question->id,
                        'option_type'   => $question->option_type,
                        'is_correct'    => 1,
                    ]);
                    $sentence->answer_id        = $answer->id;
                    $answer->sentence_id        = $sentence->id;
                    $sentence->save();
                    $answer->save();
                    $option                     = $sentence;
                } elseif(!$question->isGapFilling()) {
                    $option     = QuestionOptions::create([
                        'question_id'   => $question->id,
                        'name_en'       => 'Option '.$indexRow,
                        'option_type'   => $question->option_type,
                        'is_correct'    => 0,
                    ]);
                }

                return view($viewPath, [
                    'option'        => $option,
                    'question'      => $question,
                    'opInd'         => $indexRow,
                ]);
            });
            return $view;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function removeOption(Request $request, Questions $question){
        try {
            DB::transaction(function () use($request, $question) {
                $option = QuestionOptions::find($request->option_id);
                $option->deleted_by = Auth::id();
                $option->save();
                $option->delete();
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function changeType(Request $request, Questions $question){
        try {
            return DB::transaction(function () use($request,&$question) {
                $questionId     = $question->id;
                $fileSettings   = new QuestionSpecificFileSettings();

                $question->type = $request->input("type", 1);
                $question->content = null;
                $question->save();
                $question->refresh();

                if ($question) {
                    QuestionOptions::where(['question_id' => $questionId])->update([
                        'deleted_by'        => Auth::id(),
                        'deleted_at'        => now()
                    ]);

                    $setting                    = $question->QuestionSetting;
                    if ($setting) {
                        $setting->specific_file     = 0;
                        $setting->save();
                    }

                    $specificFilesArr = $question->questionSpecificFileSettings->pluck('id');
                    $fileSettings::whereIn('id', $specificFilesArr)
                        ->update([
                            'deleted_by' => Auth::id(),
                            'deleted_at' => now(),
                        ]);

                    QuestionSettings::updateOrCreate(
                        [
                            'question_id'       => $questionId,
                        ],
                        [
                            'specific_file'     => 0,
                            'max_file'          => 3,
                            'max_size'          => 5,
                            'created_by'        => Auth::id(),
                        ]
                    );
                }

                $this->addOption($request, $question);

                return (string) $question->content();
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
                'line'      => $th->getLine(),
            ], 500);
        }
    }

    public function optionBrowseImage(QuestionOptions $option){
        return view('forms.questions.contents.options.bowse_image', compact('option'));
    }

    public function optionUploadImage(Request $request, QuestionOptions $option)
    {
        try {
            $request->validate([
                'image' => ['required', 'image', 'max:5120'],
            ]);

            $image = DB::transaction(function () use ($request, $option) {
                if ($request->hasFile('image')) {
                    $image = $request->file('image');

                    $folder   = date('Ymd') . '/' . Auth::id();
                    $filename = date('YmdHis') . '.' . $image->extension();

                    $filePath = Storage::disk('public')->putFileAs(
                        $folder,
                        $image,
                        $filename
                    );

                    $resFile = fileStoreData([
                        'name_en' => $filename,
                        'path'    => $filePath,
                        'type'    => $image->extension(),
                        'size'    => $image->getSize(),
                    ]);

                    $option->image = $resFile->id;
                    $option->save();
                    return (string) $option->optionElImage();
                }
            });
            return response()->json([
                'image_html'   => $image,
                'success'      => true,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function optionDeleteImage(QuestionOptions $option)
    {
        try {
            DB::transaction(function () use ($option) {
                $option->image  = null;
                $option->save();
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function questionBrowseImage(Questions $question){
        return view('forms.questions.contents.images.index', compact('question'));
    }

    public function questionUploadImage(Request $request, Questions $question){
        try {
            $request->validate([
                'image' => ['required', 'image', 'max:5120'],
            ]);

            $documentHtml = DB::transaction(function () use ($request, $question) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');

                    $folder   = date('Ymd') . '/' . Auth::id();
                    $filename = date('YmdHis') . '.' . $file->extension();

                    $filePath = Storage::disk('public')->putFileAs(
                        $folder,
                        $file,
                        $filename
                    );

                    $resFile = fileStoreData([
                        'name_en' => $filename,
                        'path'    => $filePath,
                        'type'    => $file->extension(),
                        'size'    => $file->getSize(),
                    ]);

                    FormFiles::createForQuestion(
                        $question->id,
                        $resFile->id,
                        $question->section_id
                    );

                    $file = $question->files()
                        ->where('files.id', $resFile->id)
                        ->first();

                    return $file
                        ? (string) view('forms.files.view.file', compact('file'))
                        : '';
                }

                return '';
            });
            return response()->json([
                'image_html'        => $documentHtml,
                'success'           => true,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function questionDeleteImage(Questions $question)
    {
        try {
            DB::transaction(function () use ($question) {
                $question->image  = null;
                $question->save();
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function userExam(Request $request, Forms $form)
    {
        $formResponse = $this->getOrCreateUserFormResponse($form->id);
        $questionResponses = QuestionResponse::with('inputs')
            ->where('form_response_id', $formResponse->id)
            ->get()
            ->keyBy('question_id');

        foreach ($form->sections as $section) {
            foreach ($section->questions as $question) {
                $savedResponse = $questionResponses->get($question->id);
                $payload = $savedResponse
                    ? $this->mapStoredQuestionResponseToPayload($question, $savedResponse)
                    : [];

                $question->setAttribute('user_response_text', $savedResponse?->response_text);
                $question->setAttribute('user_response_json', $payload);
            }
        }

        return view('forms.views.preview', [
            'form' => $form,
            'formDuration' => $form->duration ?? 0,
            'request' => $request,
        ]);
    }

    public function saveUserQuestionResponse(Request $request, Questions $question)
    {
        try {
            $formQuestion = FormQuestions::query()
                ->where('question_id', $question->id)
                ->firstOrFail();

            $payload = DB::transaction(function () use ($request, $question, $formQuestion) {
                $formResponse = $this->getOrCreateUserFormResponse($formQuestion->form_id);
                $questionResponse = QuestionResponse::firstOrNew([
                    'form_response_id' => $formResponse->id,
                    'question_id' => $question->id,
                ]);

                if (!$questionResponse->exists) {
                    $questionResponse->created_by = Auth::id();
                }

                $questionResponse->updated_by = Auth::id();
                $questionResponse->save();

                $responseData = $this->buildUserQuestionResponseData($request, $question, $questionResponse);

                $questionResponse->response_text = $responseData['response_text'];
                $questionResponse->score = $responseData['score'];
                $questionResponse->is_correct = $responseData['is_correct'];
                $questionResponse->is_auto_correct = $responseData['is_auto_correct'];
                $questionResponse->updated_by = Auth::id();
                $questionResponse->save();

                if (!$responseData['keep_existing_inputs']) {
                    QuestionResponseInput::where('questions_response_id', $questionResponse->id)->delete();

                    foreach ($responseData['inputs'] as $input) {
                        QuestionResponseInput::create([
                            'questions_response_id' => $questionResponse->id,
                            'answer_type' => $input['answer_type'] ?? null,
                            'matching_type' => $input['matching_type'] ?? null,
                            'option_id' => $input['option_id'] ?? null,
                            'prompt_id' => $input['prompt_id'] ?? null,
                            'answer_id' => $input['answer_id'] ?? null,
                            'index' => $input['index'] ?? null,
                            'order' => $input['order'] ?? null,
                            'text_response' => $input['text_response'] ?? null,
                            'name_en' => $input['name_en'] ?? null,
                            'link' => $input['link'] ?? null,
                            'iframe_link' => $input['iframe_link'] ?? null,
                            'thumbnail' => $input['thumbnail'] ?? null,
                            'file_type' => $input['file_type'] ?? null,
                            'duration' => $input['duration'] ?? null,
                            'file_path' => $input['file_path'] ?? null,
                            'file_size' => $input['file_size'] ?? null,
                            'attachment' => $input['attachment'] ?? null,
                            'score' => $input['score'] ?? null,
                            'is_correct' => $input['is_correct'] ?? null,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ]);
                    }
                }

                $questionResponse->load('inputs');

                return $this->mapStoredQuestionResponseToPayload($question, $questionResponse);
            });

            return response()->json([
                'success' => true,
                'saved_at' => now()->toDateTimeString(),
                'response' => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    protected function getOrCreateUserFormResponse(int $formId): FormResponse
    {
        return FormResponse::firstOrCreate(
            [
                'form_id' => $formId,
                'user_id' => Auth::id(),
            ],
            [
                'starting' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );
    }

    protected function buildUserQuestionResponseData(Request $request, Questions $question, QuestionResponse $questionResponse): array
    {
        $slug = $question->answerType?->slug ?? 'input';
        $answers = $request->input('answers', []);
        $questionId = (int) $question->id;

        if ($question->isFileUpload() && !$request->hasFile('files.' . $questionId) && $questionResponse->exists) {
            return [
                'response_text' => $questionResponse->response_text,
                'inputs' => [],
                'score' => $questionResponse->score,
                'is_correct' => $questionResponse->is_correct,
                'is_auto_correct' => $questionResponse->is_auto_correct,
                'keep_existing_inputs' => true,
            ];
        }

        if ($question->isShortText() || $question->isWriting() || $question->isScq()) {
            $value = $answers[$questionId] ?? null;
            $textValue = is_scalar($value) ? trim((string) $value) : null;

            if ($question->isScq()) {
                $option = $textValue
                    ? $question->options()->where('id', $textValue)->first()
                    : null;
                $isCorrect = $option ? ((int) ($option->is_correct ?? 0) === 1) : false;
                $inputScore = $isCorrect ? $this->resolveScoreValue($question, $option, 1, 1) : 0.0;

                return [
                    'response_text' => $option?->name ?? $textValue,
                    'inputs' => $textValue ? [[
                        'answer_type' => $slug,
                        'option_id' => (int) $textValue,
                        'order' => 1,
                        'name_en' => $option?->name,
                        'score' => $inputScore,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ]] : [],
                    'score' => $inputScore,
                    'is_correct' => $textValue ? ($isCorrect ? 1 : 0) : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            if ($question->isShortText() && (int) ($question->validate_text ?? 0) === 1) {
                $expected = $this->normalizeComparableText($question->short_answer);
                $actual = $this->normalizeComparableText($textValue);
                $isCorrect = $actual !== '' && $expected !== '' && $actual === $expected;
                $inputScore = $isCorrect ? $this->resolveScoreValue($question, null, 1, 1) : 0.0;

                return [
                    'response_text' => $textValue,
                    'inputs' => $textValue !== null ? [[
                        'answer_type' => $slug,
                        'text_response' => $textValue,
                        'order' => 1,
                        'score' => $inputScore,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ]] : [],
                    'score' => $inputScore,
                    'is_correct' => $textValue !== null ? ($isCorrect ? 1 : 0) : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            return [
                'response_text' => $textValue,
                'inputs' => $textValue !== null ? [[
                    'answer_type' => $slug,
                    'text_response' => $textValue,
                    'order' => 1,
                    'score' => null,
                    'is_correct' => null,
                ]] : [],
                'score' => null,
                'is_correct' => null,
                'is_auto_correct' => 0,
                'keep_existing_inputs' => false,
            ];
        }

        if ($question->isMcq() || $question->isGapFilling() || $question->isOrdering() || $question->isMatching()) {
            $values = $answers[$questionId] ?? [];
            $values = is_array($values)
                ? array_values($values)
                : array_values(array_filter([(string) $values]));

            if ($question->isMcq()) {
                $allOptions = $question->options()->get()->keyBy('id');
                $selectedOptions = $allOptions->only(array_map('intval', $values));
                $correctOptionIds = $allOptions->filter(fn ($option) => (int) ($option->is_correct ?? 0) === 1)->keys()->map(fn ($id) => (int) $id)->values()->all();
                $selectedIds = array_values(array_map('intval', $values));
                $inputs = array_map(function ($value, $index) use ($selectedOptions, $slug, $question, $correctOptionIds) {
                    $option = $selectedOptions->get((int) $value);
                    $isCorrect = $option ? ((int) ($option->is_correct ?? 0) === 1) : false;

                    return [
                        'answer_type' => $slug,
                        'option_id' => (int) $value,
                        'order' => $index + 1,
                        'name_en' => $option?->name,
                        'score' => $isCorrect ? $this->resolveScoreValue($question, $option, count($correctOptionIds), $index + 1) : 0.0,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ];
                }, $values, array_keys($values));
                $totalScore = array_sum(array_map(fn ($input) => (float) ($input['score'] ?? 0), $inputs));
                sort($correctOptionIds);
                sort($selectedIds);

                return [
                    'response_text' => $selectedOptions->pluck('name')->filter()->implode(', '),
                    'inputs' => $inputs,
                    'score' => $totalScore,
                    'is_correct' => $selectedIds === $correctOptionIds ? 1 : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            if ($question->isGapFilling()) {
                $expectedOptions = $question->options()->orderByRaw('COALESCE(`index`, `id`), `id`')->get()->values();
                $totalItems = max(1, $expectedOptions->count());
                $inputs = array_map(function ($value, $index) use ($slug, $expectedOptions, $question, $totalItems) {
                    $expectedOption = $expectedOptions->get($index);
                    $isCorrect = $expectedOption
                        ? $this->normalizeComparableText($value) === $this->normalizeComparableText($expectedOption->name_en)
                        : false;

                    return [
                        'answer_type' => $slug,
                        'index' => $index + 1,
                        'order' => $index + 1,
                        'text_response' => $value,
                        'score' => $isCorrect ? $this->resolveScoreValue($question, $expectedOption, $totalItems, $index + 1) : 0.0,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ];
                }, $values, array_keys($values));
                $totalScore = array_sum(array_map(fn ($input) => (float) ($input['score'] ?? 0), $inputs));
                $allCorrect = !empty($inputs) && collect($inputs)->every(fn ($input) => (int) ($input['is_correct'] ?? 0) === 1) && count($inputs) === $expectedOptions->count();

                return [
                    'response_text' => implode(' | ', array_filter($values, fn ($value) => $value !== null && $value !== '')),
                    'inputs' => $inputs,
                    'score' => $totalScore,
                    'is_correct' => $allCorrect ? 1 : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            if ($question->isOrdering()) {
                $orderedOptions = $question->options()->whereIn('id', $values)->get()->keyBy('id');
                $expectedOrder = $question->options()->orderByRaw('COALESCE(`index`, `id`), `id`')->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
                $selectedOrder = array_values(array_map('intval', $values));
                $totalItems = max(1, count($expectedOrder));
                $inputs = array_map(function ($value, $index) use ($orderedOptions, $slug, $question, $expectedOrder, $totalItems) {
                    $option = $orderedOptions->get((int) $value);
                    $isCorrect = isset($expectedOrder[$index]) && (int) $expectedOrder[$index] === (int) $value;

                    return [
                        'answer_type' => $slug,
                        'option_id' => (int) $value,
                        'order' => $index + 1,
                        'name_en' => $option?->name,
                        'score' => $isCorrect ? $this->resolveScoreValue($question, $option, $totalItems, $index + 1) : 0.0,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ];
                }, $values, array_keys($values));
                $totalScore = array_sum(array_map(fn ($input) => (float) ($input['score'] ?? 0), $inputs));

                return [
                    'response_text' => collect($values)
                        ->map(fn ($value) => $orderedOptions->get((int) $value)?->name)
                        ->filter()
                        ->implode(' > '),
                    'inputs' => $inputs,
                    'score' => $totalScore,
                    'is_correct' => $selectedOrder === $expectedOrder ? 1 : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            if ($question->isMatching()) {
                $prompts = $question->options()->get()->values();
                $answersById = $question->answerSentences->whereIn('id', $values)->keyBy('id');
                $totalItems = max(1, $prompts->count());
                $inputs = array_map(function ($value, $index) use ($prompts, $answersById, $slug, $question, $totalItems) {
                    $prompt = $prompts->get($index);
                    $answer = $answersById->get((int) $value);
                    $isCorrect = $prompt ? (int) ($prompt->answer_id ?? 0) === (int) $value : false;

                    return [
                        'answer_type' => $slug,
                        'matching_type' => 'pair',
                        'prompt_id' => $prompt?->id,
                        'answer_id' => (int) $value,
                        'order' => $index + 1,
                        'name_en' => $answer?->name,
                        'score' => $isCorrect ? $this->resolveScoreValue($question, $prompt, $totalItems, $index + 1) : 0.0,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ];
                }, $values, array_keys($values));
                $totalScore = array_sum(array_map(fn ($input) => (float) ($input['score'] ?? 0), $inputs));
                $allCorrect = !empty($inputs) && collect($inputs)->every(fn ($input) => (int) ($input['is_correct'] ?? 0) === 1) && count($inputs) === $prompts->count();

                return [
                    'response_text' => collect($values)
                        ->map(fn ($value) => $answersById->get((int) $value)?->name)
                        ->filter()
                        ->implode(' | '),
                    'inputs' => $inputs,
                    'score' => $totalScore,
                    'is_correct' => $allCorrect ? 1 : 0,
                    'is_auto_correct' => 1,
                    'keep_existing_inputs' => false,
                ];
            }

            return [
                'response_text' => null,
                'inputs' => [],
                'score' => 0.0,
                'is_correct' => 0,
                'is_auto_correct' => 1,
                'keep_existing_inputs' => false,
            ];
        }

        if ($question->isTrueFalse()) {
            $values = is_array($answers) ? $answers : [];
            $allOptions = $question->options()->orderByRaw('COALESCE(`index`, `id`), `id`')->get();
            $totalItems = max(1, $allOptions->count());
            $inputs = collect($values)
                ->map(function ($value, $optionId) use ($slug, $allOptions, $question, $totalItems) {
                    $option = $allOptions->firstWhere('id', (int) $optionId);
                    $isCorrect = $option && ((string) (int) ($option->is_correct ?? 0) === (string) (int) $value);

                    return [
                        'answer_type' => $slug,
                        'option_id' => (int) $optionId,
                        'order' => (int) $optionId,
                        'text_response' => (string) $value,
                        'score' => $isCorrect ? $this->resolveScoreValue($question, $option, $totalItems, (int) $optionId) : 0.0,
                        'is_correct' => $isCorrect ? 1 : 0,
                    ];
                })
                ->values()
                ->all();
            $totalScore = array_sum(array_map(fn ($input) => (float) ($input['score'] ?? 0), $inputs));
            $allCorrect = !empty($inputs) && collect($inputs)->every(fn ($input) => (int) ($input['is_correct'] ?? 0) === 1) && count($inputs) === $allOptions->count();

            return [
                'response_text' => collect($values)
                    ->map(fn ($value, $optionId) => $optionId . ':' . $value)
                    ->implode(', '),
                'inputs' => $inputs,
                'score' => $totalScore,
                'is_correct' => $allCorrect ? 1 : 0,
                'is_auto_correct' => 1,
                'keep_existing_inputs' => false,
            ];
        }

        if ($question->isFileUpload()) {
            $files = $this->storeUploadedResponseFiles($request, $question);

            return [
                'response_text' => collect($files)->pluck('attachment')->filter()->implode(', '),
                'inputs' => array_map(function ($file, $index) use ($slug) {
                    return [
                        'answer_type' => $slug,
                        'order' => $index + 1,
                        'name_en' => $file['name_en'] ?? null,
                        'link' => $file['link'] ?? null,
                        'iframe_link' => $file['iframe_link'] ?? null,
                        'thumbnail' => $file['thumbnail'] ?? null,
                        'file_type' => $file['file_type'] ?? null,
                        'duration' => $file['duration'] ?? null,
                        'file_path' => $file['file_path'] ?? null,
                        'file_size' => $file['file_size'] ?? null,
                        'attachment' => $file['attachment'] ?? null,
                        'score' => null,
                        'is_correct' => null,
                    ];
                }, $files, array_keys($files)),
                'score' => null,
                'is_correct' => null,
                'is_auto_correct' => 0,
                'keep_existing_inputs' => false,
            ];
        }

        return [
            'response_text' => null,
            'inputs' => [],
            'score' => null,
            'is_correct' => null,
            'is_auto_correct' => 0,
            'keep_existing_inputs' => false,
        ];
    }

    protected function resolveScoreValue(Questions $question, $option = null, int $totalItems = 1, int $position = 1): float
    {
        $optionScore = $option?->getRawOriginal('score');
        if ($optionScore !== null && $optionScore !== '') {
            return (float) $optionScore;
        }

        $questionPoint = $question->getRawOriginal('point');
        if (!$question->isAssignPoint() || $questionPoint === null || $questionPoint === '') {
            return 0.0;
        }

        $itemCount = $this->countCorrectOptions($question);
        if ($itemCount < 1) {
            $itemCount = max(1, $totalItems);
        }

        return round(((float) $questionPoint) / $itemCount, 2);
    }

    protected function normalizeComparableText($value): string
    {
        return mb_strtolower(trim((string) ($value ?? '')));
    }

    protected function countCorrectOptions(Questions $question): int
    {
        return (int) $question->options()
            ->where('is_correct', 1)
            ->count();
    }

    protected function mapStoredQuestionResponseToPayload(Questions $question, QuestionResponse $questionResponse): array
    {
        $inputs = $questionResponse->inputs
            ->sortBy(fn ($input) => sprintf('%06d-%06d-%06d', $input->order ?? 999999, $input->index ?? 999999, $input->id))
            ->values();

        if ($question->isShortText() || $question->isWriting()) {
            return [
                'value' => $inputs->first()?->text_response ?? $questionResponse->response_text,
            ];
        }

        if ($question->isScq()) {
            return [
                'value' => (string) ($inputs->first()?->option_id ?? ''),
            ];
        }

        if ($question->isMcq()) {
            return [
                'values' => $inputs->pluck('option_id')->filter()->map(fn ($value) => (string) $value)->values()->all(),
            ];
        }

        if ($question->isGapFilling()) {
            return [
                'values' => $inputs->pluck('text_response')->map(fn ($value) => (string) $value)->values()->all(),
            ];
        }

        if ($question->isOrdering()) {
            return [
                'values' => $inputs->pluck('option_id')->filter()->map(fn ($value) => (string) $value)->values()->all(),
            ];
        }

        if ($question->isMatching()) {
            return [
                'values' => $inputs->pluck('answer_id')->filter()->map(fn ($value) => (string) $value)->values()->all(),
            ];
        }

        if ($question->isTrueFalse()) {
            return [
                'values' => $inputs->mapWithKeys(function ($input) {
                    return [(string) $input->option_id => (string) $input->text_response];
                })->all(),
            ];
        }

        if ($question->isFileUpload()) {
            return [
                'files' => $inputs->map(function ($input) {
                    return [
                        'name_en' => $input->name_en,
                        'link' => $input->link,
                        'iframe_link' => $input->iframe_link,
                        'thumbnail' => $input->thumbnail,
                        'file_type' => $input->file_type,
                        'duration' => $input->duration,
                        'file_path' => $input->file_path,
                        'file_size' => $input->file_size,
                        'attachment' => $input->attachment,
                    ];
                })->values()->all(),
            ];
        }

        return [];
    }

    protected function storeUploadedResponseFiles(Request $request, Questions $question): array
    {
        $uploadedFiles = $request->file('files', []);
        $questionFiles = $uploadedFiles[$question->id] ?? [];
        $storedFiles = [];

        foreach ((array) $questionFiles as $index => $file) {
            if (!$file) {
                continue;
            }

            $folder = 'form-response/' . date('Ymd') . '/' . Auth::id();
            $filename = date('YmdHis') . '_' . $question->id . '_' . $index . '.' . $file->extension();
            $path = Storage::disk('public')->putFileAs($folder, $file, $filename);

            $storedFiles[] = [
                'name_en' => $filename,
                'attachment' => $file->getClientOriginalName(),
                'link' => null,
                'iframe_link' => null,
                'thumbnail' => null,
                'file_type' => $file->extension(),
                'duration' => null,
                'file_path' => $path,
                'file_size' => $file->getSize(),
            ];
        }

        return $storedFiles;
    }
}
