<?php

namespace App\Http\Controllers\Classes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Classes\ClassesDataTble;
use App\Models\Classes\Classes;
use App\Models\Classes\Subjects;
use App\Models\Classes\ClassSubjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassesController extends Controller
{
    public function index(Request $request, ClassesDataTble $dataTable)
    {
        return $dataTable->render('classes.index');
    }

    public function create(){
        $subjects = Subjects::get();
        return view('classes.create', compact('subjects'));
    }

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            DB::transaction(function () use ($request) {
                $class = Classes::create([
                    'name'          => $request->name??null,
                    'description'   => $request->description??null,
                    'created_by'    => Auth::id(),
                ]);
                foreach ($request->subjects??[] as $subject_id) {
                    ClassSubjects::create([
                        'class_id'      => $class->id,
                        'subject_id'    => $subject_id,
                        'created_by'    => Auth::id(),
                    ]);
                }
            });

            return response()->json([
                'success'   => true,
                'title'     => __('messages.success'),
                'message'   => __('messages.success_add'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id){
        $class          = Classes::findOrFail($id);
        $subjects       = Subjects::get();
        $class_subjects = $class->classSubjects->pluck('subject_id')->toArray();
        return view('classes.edit', compact('class','subjects','class_subjects'));
    }

    public function update(Request $request, $id){
        try {
            $validator = Validator::make($request->all(), [
                'name'          => 'required|string|max:255',
                'description'   => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            DB::transaction(function () use ($request, $id) {
                $ClassSubjects      = new ClassSubjects();
                $class              = Classes::findOrFail($id);
                $class->update([
                    'name'          => $request->name,
                    'description'   => $request->description??null,
                    'updated_by'    => Auth::id(),
                ]);
                $ClassSubjects::where('class_id', $class->id)->delete();
                foreach ($request->subjects??[] as $subject_id) {
                    $ClassSubjects::create([
                        'class_id'      => $class->id,
                        'subject_id'    => $subject_id,
                        'created_by'    => Auth::id(),
                    ]);
                }
            });

            return response()->json([
                'success'   => true,
                'title'     => __('messages.success'),
                'message'   => __('messages.success_update'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        try {
            DB::transaction(function () use ($id) {
                $class               = Classes::findOrFail($id);
                $class->deleted_by   = Auth::id();
                $class->save();
                $class->delete();
            });
            return response()->json([
                'success'   => true,
                'title'     => __('messages.success'),
                'message'   => __('messages.success_delete'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
