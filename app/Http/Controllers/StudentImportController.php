<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentImportController extends Controller
{
    public function show()
    {
        return view('users.import');
    }

    public function downloadDemo()
    {
        $headers = [
            'Name',
            'DeviceUserID',
            'AdmissionNo',
            'RollNo',
            'ClassName',
            'SectionName',
            'Shift',
            'Gender',
            'BloodGroup',
            'GuardianName',
            'GuardianRelation',
            'GuardianPhone',
            'GuardianEmail',
            'Address',
        ];

        $rows = [
            [
                'Rahim Uddin',
                '1001',
                'ADM-2026-001',
                '1',
                'Class 1',
                'A',
                'Day',
                'Male',
                'O+',
                'Karim Uddin',
                'Father',
                '01700000001',
                'guardian1@example.com',
                'Dhaka',
            ],
            [
                'Fatema Akter',
                '1002',
                'ADM-2026-002',
                '2',
                'Class 1',
                'A',
                'Morning',
                'Female',
                'A+',
                'Salma Begum',
                'Mother',
                '01700000002',
                'guardian2@example.com',
                'Dhaka',
            ],
        ];

        return Response::streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'student-import-demo.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $headerMap = $this->csvHeaderMap($header ?: []);
        
        $importedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $errors = [];
        $skipped = [];
        $seenDeviceUserIds = [];
        $seenAdmissionNos = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            
            // Expected CSV Format: Name, DeviceUserID, AdmissionNo, RollNo, ClassName, SectionName, Shift, Gender, BloodGroup, GuardianName, GuardianRelation, GuardianPhone, GuardianEmail, Address
            if (count($data) < 12) {
                $errors[] = "Line {$line}: Missing required columns.";
                $failedCount++;
                continue;
            }

            $name = $this->csvValue($data, $headerMap, ['name', 'studentname', 'student_name'], 0);
            $deviceUserId = $this->csvValue($data, $headerMap, ['deviceuserid', 'device_user_id', 'deviceid', 'device_id'], 1);
            $admissionNo = $this->csvValue($data, $headerMap, ['admissionno', 'admission_no'], 2);
            $rollNo = $this->csvValue($data, $headerMap, ['rollno', 'roll_no'], 3);
            $classValue = $this->csvValue($data, $headerMap, ['classid', 'class_id', 'classname', 'class_name', 'class'], 4);
            $sectionValue = $this->csvValue($data, $headerMap, ['sectionid', 'section_id', 'sectionname', 'section_name', 'section'], 5);
            $shift = $this->csvValue($data, $headerMap, ['shift'], 6, 'Day');
            $gender = $this->csvValue($data, $headerMap, ['gender'], 7);
            $bloodGroup = $this->csvValue($data, $headerMap, ['bloodgroup', 'blood_group'], 8);
            $guardianName = $this->csvValue($data, $headerMap, ['guardianname', 'guardian_name'], 9);
            $guardianRelation = $this->csvValue($data, $headerMap, ['guardianrelation', 'guardian_relation'], 10);
            $guardianPhone = $this->csvValue($data, $headerMap, ['guardianphone', 'guardian_phone'], 11);
            $guardianEmail = $this->csvValue($data, $headerMap, ['guardianemail', 'guardian_email'], 12);
            $address = $this->csvValue($data, $headerMap, ['address'], 13);

            if ($deviceUserId && in_array($deviceUserId, $seenDeviceUserIds, true)) {
                $skippedCount++;
                $skipped[] = "Line {$line}: duplicate DeviceUserID {$deviceUserId} inside this CSV.";
                continue;
            }

            if ($admissionNo && in_array($admissionNo, $seenAdmissionNos, true)) {
                $skippedCount++;
                $skipped[] = "Line {$line}: duplicate AdmissionNo {$admissionNo} inside this CSV.";
                continue;
            }

            if ($deviceUserId && User::where('device_user_id', $deviceUserId)->exists()) {
                $skippedCount++;
                $skipped[] = "Line {$line}: DeviceUserID {$deviceUserId} already exists.";
                continue;
            }

            if ($admissionNo && User::where('admission_no', $admissionNo)->exists()) {
                $skippedCount++;
                $skipped[] = "Line {$line}: AdmissionNo {$admissionNo} already exists.";
                continue;
            }

            $class = $this->findClass($classValue);
            if (!$class) {
                $errors[] = "Line {$line}: ClassName/ClassID '{$classValue}' was not found.";
                $failedCount++;
                continue;
            }

            $section = $this->findSection($class, $sectionValue);
            if ($sectionValue !== null && !$section) {
                $errors[] = "Line {$line}: SectionName/SectionID '{$sectionValue}' was not found for {$class->name}.";
                $failedCount++;
                continue;
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
                    'device_user_id' => ['required', 'string', 'max:255'],
                    'admission_no' => ['nullable', 'string', 'max:100'],
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
                $seenDeviceUserIds[] = $deviceUserId;

                if ($admissionNo) {
                    $seenAdmissionNos[] = $admissionNo;
                }
            } catch (ValidationException $e) {
                $failedCount++;
                $errors[] = "Line {$line}: " . implode(' ', $e->validator->errors()->all());
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Line {$line}: " . $e->getMessage();
            }
        }

        fclose($handle);

        if ($skippedCount > 0 || $failedCount > 0) {
            $details = array_merge(
                array_slice($skipped, 0, 10),
                array_slice($errors, 0, 10)
            );
            $hiddenCount = max(count($skipped) + count($errors) - count($details), 0);
            $message = "Import finished. Imported: {$importedCount}. Skipped existing/duplicate: {$skippedCount}. Failed: {$failedCount}.";

            if (!empty($details)) {
                $message .= ' Details: ' . implode(', ', $details);
            }

            if ($hiddenCount > 0) {
                $message .= " ({$hiddenCount} more not shown).";
            }

            return redirect()->route('students.index')->with($failedCount > 0 ? 'warning' : 'success', $message);
        }

        return redirect()->route('students.index')->with('success', "Successfully imported {$importedCount} students.");
    }

    private function csvHeaderMap(array $header): array
    {
        $map = [];

        foreach ($header as $index => $name) {
            $key = $this->normalizeCsvKey($name);

            if ($key !== '') {
                $map[$key] = $index;
            }
        }

        return $map;
    }

    private function csvValue(array $data, array $headerMap, array $keys, int $fallbackIndex, ?string $default = null): ?string
    {
        $index = null;

        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeCsvKey($key);

            if (array_key_exists($normalizedKey, $headerMap)) {
                $index = $headerMap[$normalizedKey];
                break;
            }
        }

        $index ??= $fallbackIndex;
        $value = $data[$index] ?? $default;

        if ($value === null) {
            return $default;
        }

        $value = trim((string) $value);

        return $value === '' ? $default : $value;
    }

    private function normalizeCsvKey(?string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);

        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($value)));
    }

    private function findClass(?string $value): ?SchoolClass
    {
        if (!$value) {
            return null;
        }

        if (ctype_digit($value)) {
            $class = SchoolClass::find((int) $value);

            if ($class) {
                return $class;
            }
        }

        $normalizedValue = $this->normalizeClassName($value);

        return SchoolClass::all()->first(function (SchoolClass $class) use ($normalizedValue) {
            return $this->normalizeClassName($class->name) === $normalizedValue;
        });
    }

    private function findSection(SchoolClass $class, ?string $value): ?Section
    {
        if (!$value) {
            return null;
        }

        if (ctype_digit($value)) {
            $section = Section::where('class_id', $class->id)->find((int) $value);

            if ($section) {
                return $section;
            }
        }

        $normalizedValue = $this->normalizeCsvKey($value);

        return Section::where('class_id', $class->id)->get()->first(function (Section $section) use ($normalizedValue) {
            return $this->normalizeCsvKey($section->name) === $normalizedValue;
        });
    }

    private function normalizeClassName(string $value): string
    {
        $value = strtolower(trim($value));
        $numberWords = [
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => '4',
            'five' => '5',
            'six' => '6',
            'seven' => '7',
            'eight' => '8',
            'nine' => '9',
            'ten' => '10',
            'eleven' => '11',
            'twelve' => '12',
        ];

        foreach ($numberWords as $word => $number) {
            $value = preg_replace('/\b' . $word . '\b/', $number, $value);
        }

        return preg_replace('/[^a-z0-9]/', '', $value);
    }
}
