<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentImportController extends Controller
{
    public function show()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle); // Skip header
        
        $importedCount = 0;
        $errors = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            
            // Expected CSV Format: Name, DeviceUserID, AdmissionNo, RollNo, ClassName, SectionName, Shift, Gender, BloodGroup, GuardianName, GuardianRelation, GuardianPhone, GuardianEmail, Address
            if (count($data) < 12) {
                $errors[] = "Line {$line}: Missing required columns.";
                continue;
            }

            $name = $data[0];
            $deviceUserId = $data[1];
            $admissionNo = $data[2] ?? null;
            $rollNo = $data[3] ?? null;
            $className = $data[4] ?? null;
            $sectionName = $data[5] ?? null;
            $shift = $data[6] ?? 'Day';
            $gender = $data[7] ?? null;
            $bloodGroup = $data[8] ?? null;
            $guardianName = $data[9] ?? null;
            $guardianRelation = $data[10] ?? null;
            $guardianPhone = $data[11] ?? null;
            $guardianEmail = $data[12] ?? null;
            $address = $data[13] ?? null;

            // Find Class
            $class = SchoolClass::where('name', $className)->first();
            $section = null;
            if ($class && $sectionName) {
                $section = Section::where('class_id', $class->id)->where('name', $sectionName)->first();
            }

            try {
                validator([
                    'name' => $name,
                    'device_user_id' => $deviceUserId,
                    'admission_no' => $admissionNo,
                    'roll_no' => $rollNo,
                    'class_id' => $class?->id,
                    'section_id' => $section?->id,
                    'shift' => $shift,
                    'gender' => $gender,
                    'blood_group' => $bloodGroup,
                    'guardian_name' => $guardianName,
                    'guardian_phone' => $guardianPhone,
                    'guardian_email' => $guardianEmail,
                ], [
                    'name' => ['required', 'string', 'max:255'],
                    'device_user_id' => ['required', 'string', 'max:255', 'unique:users,device_user_id'],
                    'admission_no' => ['nullable', 'string', 'max:100', 'unique:users,admission_no'],
                    'roll_no' => [
                        'required',
                        'string',
                        'max:50',
                        Rule::unique('users', 'roll_no')
                            ->where(fn ($query) => $query
                                ->where('class_id', $class?->id)
                                ->where('section_id', $section?->id)
                            ),
                    ],
                    'class_id' => ['required', 'exists:classes,id'],
                    'section_id' => ['nullable', 'exists:sections,id'],
                    'shift' => ['nullable', 'in:Morning,Day'],
                    'gender' => ['required', 'in:Male,Female,Other'],
                    'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                    'guardian_name' => ['required', 'string', 'max:255'],
                    'guardian_phone' => ['required', 'string', 'max:20'],
                    'guardian_email' => ['nullable', 'email', 'max:255'],
                ])->validate();

                $user = User::create([
                    'name' => $name,
                    'email' => 'student-' . Str::uuid() . '@no-login.local',
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'student',
                    'device_user_id' => $deviceUserId,
                    'admission_no' => $admissionNo,
                    'class_id' => $class->id,
                    'section_id' => $section?->id,
                    'roll_no' => $rollNo,
                    'shift' => $shift,
                    'gender' => $gender,
                    'blood_group' => $bloodGroup,
                    'guardian_name' => $guardianName,
                    'guardian_relation' => $guardianRelation,
                    'guardian_phone' => $guardianPhone,
                    'guardian_email' => $guardianEmail,
                    'address' => $address,
                ]);
                
                $user->assignRole('student');
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Line {$line}: " . $e->getMessage();
            }
        }

        fclose($handle);

        if (!empty($errors)) {
            return redirect()->route('students.index')->with('warning', "Imported {$importedCount} students with some errors: " . implode(', ', $errors));
        }

        return redirect()->route('students.index')->with('success', "Successfully imported {$importedCount} students.");
    }
}
