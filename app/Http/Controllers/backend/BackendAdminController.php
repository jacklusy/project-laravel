<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\department;
use App\Models\member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BackendAdminController extends Controller
{
    public function UserStore(Request $request)
    {

        if ($request->user_id != null) {

            $user = User::find($request->user_id);
            if (!$user) {
                abort(404);
            }

            if ($request->input('status') == true) {
                if ($request->input('password') == null) {

                    $user->update([

                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'email' =>  $request->input('email'),
                        'phone' => $request->input('phone'),
                        'gender' =>  $request->input('gender'),
                        'status' => 'active',

                    ]);
                } else {
                    $user->update([

                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'email' =>  $request->input('email'),
                        'password' => Hash::make($request->input('password')),
                        'phone' => $request->input('phone'),
                        'gender' =>  $request->input('gender'),
                        'status' => 'active',

                    ]);
                }
            } else {

                if ($request->input('password') == null) {

                    $user->update([

                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'email' =>  $request->input('email'),
                        'phone' => $request->input('phone'),
                        'gender' =>  $request->input('gender'),
                        'status' => 'inactive',

                    ]);
                } else {
                    $user->update([

                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'email' =>  $request->input('email'),
                        'password' => Hash::make($request->input('password')),
                        'phone' => $request->input('phone'),
                        'gender' =>  $request->input('gender'),
                        'status' => 'inactive',

                    ]);
                }
            }
            // 'password' => Hash::make($request->input('password')),

            return response()->json([
                'success' => 'User Updated Successfully'
            ], 201);
        } else {

            $request->validate([
                'firstname' => 'required|alpha|min:2|max:30',
                'lastname' => 'required|alpha',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'phone' => 'nullable|digits:10',
                'gender' => 'required',
            ]);

            User::insert([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'created_at' =>Carbon::now(),
            ]);

            return response()->json([
                'success' => 'User Add Successfully'
            ], 201);
        }
    }

    public function index()
    {

        $users = User::where('role', "!=", 'admin')->get();
        if (!$users) {
            abort(404);
        }
        return $users;
    }

    public function UserEdit($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        return $user;
    }

    public function UserDelete($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        $user->delete();
        return response()->json([
            'success' => 'User Deleted Successfully'
        ], 201);
    }



    /////////////// Departments /////////////////

    public function departmentsIndex() {
        return view('admin.departments.department');
    }


    public function DepartmentsStore(Request $request) {
        $request->validate([
            'departmentName' => 'required' ,
        ]);

        department::insert([
            'departmentName' => $request->departmentName,
            'created_at' =>Carbon::now(),

        ]);

        return response()->json([
            'success' => 'Department Created Successfully'
        ],201);
    }


    public function AllDepartment()
    {
        $department = department::all();
        if (! $department) {
            abort(404);
        }
        return $department;
    }

    public function StudentAllAjax($AllCourse) 
    {
        $Members = member::where('department_id', $AllCourse)->get();
        $userMem = $Members->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $userMem)->where('role','!=','admin')->get();

        return json_encode($users);
    }


    public function MemberStore(Request $request) {
        $request->validate([
            'department_id' => 'required' ,
            'user_id' => 'required' ,
        ]);

        member::create([
            'department_id' => $request->department_id,
            'user_id' => $request->user_id,
            'created_at' =>Carbon::now(),

        ]);

        return response()->json([
            'success' => 'Member Created Successfully'
        ],201);

        
    }

    public function GetDataMembers()
    {
        $members = member::all();
        if (! $members) {
            abort(404);
        }
        return $members;
    }


}
