<?php

namespace App\Http\Controllers\Form;

use App\Models\Files;
use App\Models\Form\Forms;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Form\FormFiles;
use App\Models\Form\Questions;
use App\Models\Form\FormSections;
use App\Models\Form\FormQuestions;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Form\QuestionAnswerTypes;

class FormController extends Controller
{
    protected $question_types;

    public function __construct()
    {
        $this->question_types = QuestionAnswerTypes::get()->pluck('name','id');
    }

    public function list(){
        $forms = Forms::ByCreator()->latest()->paginate(10);
        return view('forms.index',compact('forms'));
    }

    public function create(){
       try {
            $form = DB::transaction(function () {
                $form = Forms::create([
                    'uuid'              => Str::uuid(),
                    'name_en'           => "Untitled",
                    'created_by'        => Auth::id(),
                ]);
                $section = FormSections::create([
                    'form_id'           => $form->id,
                    'name_en'           => "Untitled",
                    'created_by'        => Auth::id(),
                    'order'             => 1,
                ]);
                $question = Questions::create([
                    'name_en'           => null,
                    'type'              => 1,
                    'created_by'        => Auth::id(),
                ]);
                FormQuestions::create([
                    'form_id'           => $form->id,
                    'form_section_id'   => $section->id,
                    'question_id'       => $question->id,
                    'order'             => 1,
                ]);
                return $form;
            });
        return response()->json(['form' => $form], 200);
       } catch (\Throwable $th) {
        throw $th;
       }
    }

    public function index(Request $request, Forms $form){
        $qans_types = QuestionAnswerTypes::get()->pluck('name', 'id');
        $datas      = array(
            'form'          => $form,
            'qans_types'    => $qans_types
        );
       return view('forms.form', $datas);
    }

    public function formDeleteFile(Request $request, Files $file){
        try {
            DB::transaction(function () use ($request, $file) {
                $sectionFile = FormFiles::where(function($query) use ($request, $file){
                    if($request->file_for == 'section'){
                        $query->where('section_id', $request->section_id);
                    } elseif($request->file_for == 'question'){
                        $query->where('question_id', $request->question_id);
                    }
                    $query->where('file_id', $file->id);
                })->first();
                $sectionFile->update(['deleted_by' => Auth::id()]);
                $sectionFile->save();
                $sectionFile->delete();
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

    public function preview(Request $request, Forms $form){
        $qans_types     = QuestionAnswerTypes::get()->pluck('name', 'id');
        $formDuration   = $form->duration ?? 0;
        $datas      = array(
            'form'              => $form,
            'qans_types'        => $qans_types,
            'formDuration'      => $formDuration,
            'request'           => $request
        );
       return view('forms.views.preview', $datas);
    }

    public function updateSettings(Request $request, Forms $form){
        try {
            DB::transaction(function () use ($request, $form) {
                $form->update([
                    'duration'              => $request->duration ?? 0,
                    'publish_result_date'   => defaultDate($request->publish_result_date),
                ]);
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
                'line'      => $th->getLine(),
            ], 500);
        }
    }

    public function publicForm(Request $request, Forms $form){
        try {
            DB::transaction(function () use ($request, $form) {
                $shortUrl = $this->generateShortUrl($form->uuid);
                $form->update([
                    'assign_status'    => 1,
                    'shared_link'      => $shortUrl,
                ]);
            });
            return response()->json([
                'success'   => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'error'     => $th->getMessage(),
                'line'      => $th->getLine(),
            ], 500);
        }
    }

    protected function generateShortUrl($uuid)
    {
        return route('forms.exam.index', [
            'form' => $uuid
        ]);
    }
}
