<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\TimeTable;
use App\Models\SyncLog;
use App\Models\AttendanceRawLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Device;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance logs with filters.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user.schoolClass', 'user.section'])->orderBy('date', 'desc');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->role($request->role);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('section_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        $attendances = $query->paginate(10);
        $classes = SchoolClass::all();
        $sections = Section::all();
        $roles = Role::all();
        
        return view('attendances.index', compact('attendances', 'classes', 'sections', 'roles'));
    }

    /**
     * Show form to add manual attendance.
     */
    public function create()
    {
        $users = User::all();
        return view('attendances.create', compact('users'));
    }

    /**
     * Store manual attendance entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|in:Present,Late,Absent,Half Day,Missing Punch,Leave',
        ]);

        Attendance::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            $request->all()
        );

        return redirect()->route('attendances.index')->with('success', 'Manual attendance recorded.');
    }

    /**
     * API endpoint for ZKTeco logs synchronization.
     */
    public function sync(Request $request)
    {
        if (!$this->hasValidSyncToken($request)) {
            return response()->json(['message' => 'Invalid sync token.'], 401);
        }

        $logs = $request->input('logs', []);
        
        if (empty($logs)) {
            return response()->json(['message' => 'No logs provided.'], 400);
        }

        $processedCount = 0;
        $duplicateCount = 0;
        $failedCount = 0;
        $errors = [];
        $datesToFinalize = [];
        $device = $this->resolveDevice($request);

        foreach ($logs as $log) {
            $validator = Validator::make($log, [
                'device_user_id' => 'required|string',
                'timestamp' => 'required|date',
            ]);

            if ($validator->fails()) {
                $failedCount++;
                $errors[] = $validator->errors()->first();
                continue;
            }

            $deviceUserId = (string) $log['device_user_id'];
            $timestamp = Carbon::parse($log['timestamp']);

            if ($this->isInvalidPunchTime($timestamp)) {
                $failedCount++;
                $errors[] = "Ignored invalid punch for device user {$deviceUserId} at {$timestamp}.";
                continue;
            }

            $date = $timestamp->toDateString();
            $time = $timestamp->toTimeString();
            $datesToFinalize[$date] = true;

            $rawLog = AttendanceRawLog::firstOrCreate(
                [
                    'device_user_id' => $deviceUserId,
                    'punch_time' => $timestamp,
                ],
                [
                    'device_id' => $device?->id,
                    'status' => 'received',
                ]
            );

            if (!$rawLog->wasRecentlyCreated) {
                $duplicateCount++;
                continue;
            }

            // 1. Find the user
            $user = User::where('device_user_id', $deviceUserId)->first();
            if (!$user) {
                $rawLog->update([
                    'status' => 'failed',
                    'error' => 'No matching user found.',
                ]);
                $failedCount++;
                $errors[] = "No user found for device user {$deviceUserId}.";
                continue; 
            }

            $rawLog->update(['user_id' => $user->id]);

            // 2. Check if Holiday or Approved Leave
            if ($this->isHolidayOrLeave($user, $date)) {
                $rawLog->update(['status' => 'skipped']);
                continue; 
            }

            // 3. Find or create attendance for the day
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $user->id, 'date' => $date],
                ['status' => 'Absent']
            );

            // 4. Update Check-in
            if (!$attendance->check_in || $timestamp->lt(Carbon::parse($date . ' ' . $attendance->check_in))) {
                $attendance->check_in = $time;
            }

            // 5. Update Check-out
            if (!$attendance->check_out || $timestamp->gt(Carbon::parse($date . ' ' . $attendance->check_out))) {
                $attendance->check_out = $time;
            }

            // 6. Calculate Status
            $this->calculateStatus($attendance, $user);
            
            $attendance->save();
            $rawLog->update(['status' => 'processed']);
            $processedCount++;
        }

        foreach (array_keys($datesToFinalize) as $date) {
            $this->markAbsentUsersForDate($date);
        }

        // Record Sync Log
        SyncLog::create([
            'device_id' => $device?->id,
            'last_sync_time' => Carbon::now(),
            'total_records' => count($logs),
            'processed_records' => $processedCount,
            'duplicate_records' => $duplicateCount,
            'failed_records' => $failedCount,
            'errors' => empty($errors) ? null : implode("\n", array_slice($errors, 0, 20)),
        ]);

        return response()->json([
            'message' => 'Attendance synchronized.',
            'processed_count' => $processedCount,
            'duplicate_count' => $duplicateCount,
            'failed_count' => $failedCount,
            'last_sync' => Carbon::now()->toDateTimeString()
        ]);
    }

    public function latestSync(Request $request)
    {
        if (!$this->hasValidSyncToken($request)) {
            return response()->json(['message' => 'Invalid sync token.'], 401);
        }

        $device = $this->resolveDevice($request);
        $query = SyncLog::query();

        if ($device) {
            $query->where('device_id', $device->id);
        }

        $lastSync = $query->latest('last_sync_time')->value('last_sync_time');

        return response()->json([
            'last_sync_time' => $lastSync ? Carbon::parse($lastSync)->toDateTimeString() : null,
        ]);
    }

    /**
     * Check if holiday or leave.
     */
    private function isHolidayOrLeave(User $user, $date)
    {
        if (Holiday::where('date', $date)->exists()) return true;

        $onLeave = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();

        if ($onLeave) {
            Attendance::updateOrCreate(
                ['user_id' => $user->id, 'date' => $date],
                ['status' => 'Leave']
            );
            return true;
        }

        return false;
    }

    /**
     * Calculate status.
     */
    private function calculateStatus(Attendance $attendance, User $user)
    {
        $dayOfWeek = Carbon::parse($attendance->date)->format('l'); 
        
        $query = TimeTable::where('role', $user->role)->where('day', $dayOfWeek);
        
        if ($user->role === 'student' && $user->class_id) {
            $query->where('class_id', $user->class_id);
        } else {
            $query->whereNull('class_id'); 
        }

        $timetable = $query->first();

        if (!$timetable) return;

        if (!$attendance->check_in) {
            $attendance->status = 'Absent';
            return;
        }

        $checkIn = Carbon::parse($attendance->date . ' ' . $attendance->check_in);
        $checkOut = $attendance->check_out ? Carbon::parse($attendance->date . ' ' . $attendance->check_out) : null;
        
        $inTime = Carbon::parse($attendance->date . ' ' . $timetable->in_time);
        $lateTime = Carbon::parse($attendance->date . ' ' . $timetable->late_time);
        $outTime = Carbon::parse($attendance->date . ' ' . $timetable->out_time);
        $graceTime = $timetable->grace_time ?? 0;
        $halfDayTime = $timetable->half_day_time ? Carbon::parse($attendance->date . ' ' . $timetable->half_day_time) : null;

        if ($halfDayTime && $checkIn->gt($halfDayTime)) {
            $attendance->status = 'Half Day';
        }
        elseif ($checkIn->gt($lateTime->copy()->addMinutes($graceTime))) {
            $attendance->status = 'Late';
        }
        else {
            $attendance->status = 'Present';
        }

        if (!$checkOut || ($attendance->check_in === $attendance->check_out && Carbon::now()->gt($outTime))) {
             $attendance->status = 'Missing Punch';
        }

        if ($checkOut) {
            $attendance->working_hours = round($checkIn->floatDiffInHours($checkOut), 2);

            if ($timetable->overtime_start) {
                $overtimeStart = Carbon::parse($attendance->date . ' ' . $timetable->overtime_start);
                $attendance->overtime_hours = $checkOut->gt($overtimeStart)
                    ? round($overtimeStart->floatDiffInHours($checkOut), 2)
                    : 0;
            }
        }

        if ($checkOut && $attendance->status !== 'Missing Punch') {
            if ($checkOut->lt($outTime)) {
                $attendance->early_leave = true;
            } else {
                $attendance->early_leave = false;
            }
        }
    }

    private function hasValidSyncToken(Request $request): bool
    {
        $token = config('services.attendance_sync.token');

        if (!$token) {
            return app()->environment('local');
        }

        $providedToken = $request->bearerToken() ?: $request->header('X-Sync-Token');

        return is_string($providedToken) && hash_equals($token, $providedToken);
    }

    private function resolveDevice(Request $request): ?Device
    {
        if ($request->filled('device_id')) {
            return Device::find($request->input('device_id'));
        }

        if ($request->filled('device_ip')) {
            return Device::where('ip_address', $request->input('device_ip'))->first();
        }

        return null;
    }

    private function isInvalidPunchTime(Carbon $timestamp): bool
    {
        if ($timestamp->isFuture()) {
            return true;
        }

        return $timestamp->format('H:i:s') === '00:00:00';
    }

    private function markAbsentUsersForDate(string $date): void
    {
        if (Holiday::where('date', $date)->exists()) {
            return;
        }

        $dayOfWeek = Carbon::parse($date)->format('l');

        User::query()
            ->whereIn('role', ['student', 'teacher'])
            ->whereDoesntHave('attendances', function ($query) use ($date) {
                $query->where('date', $date);
            })
            ->chunkById(100, function ($users) use ($date, $dayOfWeek) {
                foreach ($users as $user) {
                    if ($this->isHolidayOrLeave($user, $date)) {
                        continue;
                    }

                    $timetable = TimeTable::where('role', $user->role)
                        ->where('day', $dayOfWeek)
                        ->when($user->role === 'student', function ($query) use ($user) {
                            $query->where('class_id', $user->class_id);
                        }, function ($query) {
                            $query->whereNull('class_id');
                        })
                        ->first();

                    if ($timetable) {
                        Attendance::firstOrCreate(
                            ['user_id' => $user->id, 'date' => $date],
                            ['status' => 'Absent']
                        );
                    }
                }
            });
    }

    /**
     * Batch delete attendance logs.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No logs selected.']);
        }

        Attendance::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success', 'message' => count($ids) . ' logs deleted.']);
    }

    /**
     * Trigger Multi-Device Sync.
     */
    public function triggerSync()
    {
        $devices = Device::where('status', true)->get();
        
        if ($devices->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No active devices found.']);
        }

        $results = [];
        $apiUrl = url('/api/attendance-sync');

        foreach ($devices as $device) {
            $commandParts = [
                'python',
                'sync_attendance.py',
                $device->ip_address,
                $apiUrl,
                (string) $device->port,
            ];

            if (config('services.attendance_sync.token')) {
                $commandParts[] = config('services.attendance_sync.token');
            }

            $command = implode(' ', array_map('escapeshellarg', $commandParts));
            $output = shell_exec($command);
            
            $results[] = [
                'device' => $device->name,
                'output' => $output
            ];
            
            // Update last online time
            $device->update(['last_online_at' => Carbon::now()]);
        }

        Log::info("Multi-device sync triggered. Results: " . json_encode($results));

        return response()->json([
            'status' => 'success',
            'message' => 'Multi-device sync completed.',
            'details' => $results
        ]);
    }
}
