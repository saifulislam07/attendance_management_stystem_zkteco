<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Support\TablePerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['schoolClass', 'section'])->where('role', '!=', 'student');

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $users = $query->paginate(TablePerPage::resolve($request));
        $classes = SchoolClass::all();
        $sections = Section::all();
        $roles = Role::all();

        return view('users.index', compact('users', 'classes', 'sections', 'roles'));
    }

    /**
     * Display only student users.
     */
    public function students(Request $request)
    {
        $query = User::with(['schoolClass', 'section'])->role('student');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $users = $query->paginate(TablePerPage::resolve($request));
        $classes = SchoolClass::all();
        $sections = Section::all();
        $roles = Role::all();
        $isStudentList = true;
        $pageTitle = 'Students';
        $filterRoute = route('students.index');

        return view('students.index', compact(
            'users',
            'classes',
            'sections',
            'roles',
            'isStudentList',
            'pageTitle',
            'filterRoute'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $selectedRole = request('role', 'student');
        $isStudentCreate = $selectedRole === 'student';

        return view('users.create', compact('classes', 'sections', 'selectedRole', 'isStudentCreate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->input('role') === 'student') {
            $request->merge([
                'email' => $request->filled('email')
                    ? $request->email
                    : 'student-' . Str::uuid() . '@no-login.local',
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|required_unless:role,student|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'device_user_id' => 'required|string|unique:users,device_user_id',
            'admission_no' => 'nullable|string|max:100|unique:users,admission_no',
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'section_id' => 'nullable|exists:sections,id',
            'roll_no' => [
                'nullable',
                'required_if:role,student',
                'string',
                'max:50',
                Rule::unique('users', 'roll_no')
                    ->where(fn ($query) => $query
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                    ),
            ],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'shift' => 'nullable|string|in:Morning,Day',
            'phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|required_if:role,student|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|required_if:role,student|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->role === 'student' ? Str::random(32) : $request->password),
            'role' => $request->role,
            'device_user_id' => $request->device_user_id,
            'admission_no' => $request->role === 'student' ? $request->admission_no : null,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
            'section_id' => $request->section_id,
            'roll_no' => $request->role === 'student' ? $request->roll_no : null,
            'date_of_birth' => $request->role === 'student' ? $request->date_of_birth : null,
            'gender' => $request->role === 'student' ? $request->gender : null,
            'blood_group' => $request->role === 'student' ? $request->blood_group : null,
            'shift' => $request->shift,
            'phone' => $request->phone,
            'guardian_name' => $request->role === 'student' ? $request->guardian_name : null,
            'guardian_relation' => $request->role === 'student' ? $request->guardian_relation : null,
            'guardian_phone' => $request->role === 'student' ? $request->guardian_phone : null,
            'guardian_email' => $request->role === 'student' ? $request->guardian_email : null,
            'address' => $request->role === 'student' ? $request->address : null,
        ]);

        $user->assignRole($request->role);

        $route = $request->role === 'student' ? 'students.index' : 'users.index';
        $message = $request->role === 'student' ? 'Student created successfully.' : 'User created successfully.';

        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        return view('users.edit', compact('user', 'classes', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($request->input('role') === 'student' && !$request->filled('email')) {
            $request->merge([
                'email' => $user->email ?: 'student-' . Str::uuid() . '@no-login.local',
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'device_user_id' => 'required|string|unique:users,device_user_id,' . $user->id,
            'admission_no' => 'nullable|string|max:100|unique:users,admission_no,' . $user->id,
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'section_id' => 'nullable|exists:sections,id',
            'roll_no' => [
                'nullable',
                'required_if:role,student',
                'string',
                'max:50',
                Rule::unique('users', 'roll_no')
                    ->ignore($user->id)
                    ->where(fn ($query) => $query
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                    ),
            ],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'shift' => 'nullable|string|in:Morning,Day',
            'phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|required_if:role,student|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|required_if:role,student|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'device_user_id' => $request->device_user_id,
            'admission_no' => $request->role === 'student' ? $request->admission_no : null,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
            'section_id' => $request->section_id,
            'roll_no' => $request->role === 'student' ? $request->roll_no : null,
            'date_of_birth' => $request->role === 'student' ? $request->date_of_birth : null,
            'gender' => $request->role === 'student' ? $request->gender : null,
            'blood_group' => $request->role === 'student' ? $request->blood_group : null,
            'shift' => $request->shift,
            'phone' => $request->phone,
            'guardian_name' => $request->role === 'student' ? $request->guardian_name : null,
            'guardian_relation' => $request->role === 'student' ? $request->guardian_relation : null,
            'guardian_phone' => $request->role === 'student' ? $request->guardian_phone : null,
            'guardian_email' => $request->role === 'student' ? $request->guardian_email : null,
            'address' => $request->role === 'student' ? $request->address : null,
        ];

        if ($request->role !== 'student' && $request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->role);

        $route = $request->role === 'student' ? 'students.index' : 'users.index';
        $message = $request->role === 'student' ? 'Student updated successfully.' : 'User updated successfully.';

        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Batch delete users.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No users selected.']);
        }

        // Security: Prevent self-deletion
        $ids = array_diff($ids, [auth()->id()]);

        // Manually handle foreign key constraints for attendance logs
        \App\Models\Attendance::whereIn('user_id', $ids)->delete();

        User::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success', 'message' => count($ids) . ' users deleted.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        \App\Models\Attendance::where('user_id', $user->id)->delete();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
