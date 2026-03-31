<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            
            // Expected CSV Format: Name, Email, DeviceUserID, ClassName, SectionName, Shift, Phone, Password
            if (count($data) < 3) continue;

            $name = $data[0];
            $email = $data[1];
            $deviceUserId = $data[2];
            $className = $data[3] ?? null;
            $sectionName = $data[4] ?? null;
            $shift = $data[5] ?? 'Day';
            $phone = $data[6] ?? null;
            $password = $data[7] ?? 'password123';

            // Find Class
            $class = SchoolClass::where('name', $className)->first();
            $section = null;
            if ($class && $sectionName) {
                $section = Section::where('class_id', $class->id)->where('name', $sectionName)->first();
            }

            try {
                $user = User::updateOrCreate(
                    ['device_user_id' => $deviceUserId],
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => 'student', // Keeping legacy column for sync
                        'class_id' => $class->id ?? null,
                        'section_id' => $section->id ?? null,
                        'shift' => $shift,
                        'phone' => $phone
                    ]
                );
                
                $user->assignRole('student');
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Line {$line}: " . $e->getMessage();
            }
        }

        fclose($handle);

        if (!empty($errors)) {
            return redirect()->route('users.index')->with('warning', "Imported {$importedCount} students with some errors: " . implode(', ', $errors));
        }

        return redirect()->route('users.index')->with('success', "Successfully imported {$importedCount} students.");
    }
}
