<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use App\Support\TablePerPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
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

        $users = $query->latest()->paginate(TablePerPage::resolve($request));
        $classes = SchoolClass::all();
        $sections = Section::all();

        return view('students.index', compact('users', 'classes', 'sections'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();

        return view('students.create', compact('classes', 'sections'));
    }

    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $student->load(['schoolClass', 'section']);

        return view('students.show', compact('student'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateStudent($request);
        $validated['email'] = 'student-' . Str::uuid() . '@no-login.local';
        $validated['password'] = Hash::make(Str::random(32));
        $validated['role'] = 'student';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = User::create($validated);
        $student->assignRole('student');

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function edit(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $classes = SchoolClass::all();
        $sections = Section::all();

        return view('students.edit', compact('student', 'classes', 'sections'));
    }

    public function update(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $validated = $this->validateStudent($request, $student);
        $validated['email'] = $student->email ?: 'student-' . Str::uuid() . '@no-login.local';
        $validated['role'] = 'student';

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);
        $student->syncRoles('student');

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        abort_unless($student->role === 'student', 404);

        \App\Models\Attendance::where('user_id', $student->id)->delete();

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    private function validateStudent(Request $request, ?User $student = null): array
    {
        $studentId = $student?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'device_user_id' => ['required', 'string', 'max:255', Rule::unique('users', 'device_user_id')->ignore($studentId)],
            'admission_no' => ['nullable', 'string', 'max:100', Rule::unique('users', 'admission_no')->ignore($studentId)],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'roll_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'roll_no')
                    ->ignore($studentId)
                    ->where(fn ($query) => $query
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                    ),
            ],
            'shift' => ['nullable', 'string', 'in:Morning,Day'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
