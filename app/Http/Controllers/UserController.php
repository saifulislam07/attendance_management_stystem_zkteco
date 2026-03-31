<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['schoolClass', 'section']);

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

        $users = $query->paginate(10);
        $classes = SchoolClass::all();
        $sections = Section::all();
        $roles = Role::all();

        return view('users.index', compact('users', 'classes', 'sections', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        return view('users.create', compact('classes', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'device_user_id' => 'required|string|unique:users,device_user_id',
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'section_id' => 'nullable|exists:sections,id',
            'shift' => 'nullable|string|in:Morning,Day',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'device_user_id' => $request->device_user_id,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
            'section_id' => $request->section_id,
            'shift' => $request->shift,
            'phone' => $request->phone,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'device_user_id' => 'required|string|unique:users,device_user_id,' . $user->id,
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'section_id' => 'nullable|exists:sections,id',
            'shift' => 'nullable|string|in:Morning,Day',
            'phone' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'device_user_id' => $request->device_user_id,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
            'section_id' => $request->section_id,
            'shift' => $request->shift,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
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
