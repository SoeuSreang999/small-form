<?php

namespace App\Http\Controllers\Classes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Classes\SubjectDataTble;
use App\Models\Classes\Subjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\SubjectCreate;

class SubjectsController extends Controller
{
    public function index(Request $request, SubjectDataTble $dataTable)
    {
        return $dataTable->render('classes.subject.index');
    }

    public function create(){
        return view('classes.subject.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'              => 'required|string|max:255',
                'description'       => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            DB::transaction(function () use ($request) {
                $subject                       = new Subjects;
                $subject->name                 = $request->name;
                $subject->description          = $request->description;
                $subject->updated_at           = now();
                $subject->updated_by           = Auth::id();
                $subject->save();
                // event(new SubjectCreate($subject));
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

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $row = Subjects::findOrFail($id);
                $row->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id){
        $subject = Subjects::findOrFail($id);
        return view('classes.subject.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'              => 'required|string|max:255',
                'description'       => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            DB::transaction(function () use ($request, $id) {
                $subject = Subjects::findOrFail($id);
                $subject->name                 = $request->name;
                $subject->description          = $request->description;
                $subject->updated_at           = now();
                $subject->updated_by           = Auth::id();
                $subject->save();
                // event(new SubjectCreate($subject));
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
}
