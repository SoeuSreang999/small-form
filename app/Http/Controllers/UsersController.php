<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request, UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create() {}

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'date_of_birth' => 'required|date_format:d-m-Y',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'role_id'       => 'required|integer',
            'password'      => 'nullable|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->first_name    = $request->first_name;
        $user->last_name     = $request->last_name;
        $user->email         = $request->email;
        $user->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
        $user->phone         = $request->phone;
        $user->gender        = $request->gender;
        $user->role_id       = $request->role_id;
        if (! empty($validated['password'])) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'data' => $user,
        ]);

    }
}
