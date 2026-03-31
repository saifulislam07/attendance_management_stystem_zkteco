<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\TimeTable;
use App\Models\SyncLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Device;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        $logs = $request->input('logs', []);
        
        if (empty($logs)) {
            return response()->json(['message' => 'No logs provided.'], 400);
        }

        $processedCount = 0;

        foreach ($logs as $log) {
            $deviceUserId = $log['device_user_id'];
            $timestamp = Carbon::parse($log['timestamp']);
            $date = $timestamp->toDateString();
            $time = $timestamp->toTimeString();

            // 1. Find the user
            $user = User::where('device_user_id', $deviceUserId)->first();
            if (!$user) {
                continue; 
            }

            // 2. Check if Holiday or Approved Leave
            if ($this->isHolidayOrLeave($user, $date)) {
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
            $processedCount++;
        }

        // Record Sync Log
        SyncLog::create(['last_sync_time' => Carbon::now()]);

        return response()->json([
            'message' => 'Attendance synchronized.',
            'processed_count' => $processedCount,
            'last_sync' => Carbon::now()->toDateTimeString()
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

        $checkIn = Carbon::parse($attendance->date . ' ' . $attendance->check_in);
        $checkOut = Carbon::parse($attendance->date . ' ' . $attendance->check_out);
        
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

        if ($attendance->check_in === $attendance->check_out && Carbon::now()->gt($outTime)) {
             $attendance->status = 'Missing Punch';
        }

        if ($attendance->check_out && $attendance->status !== 'Missing Punch') {
            if ($checkOut->lt($outTime)) {
                $attendance->early_leave = true;
            } else {
                $attendance->early_leave = false;
            }
        }
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
            $command = "python sync_attendance.py {$device->ip_address} {$apiUrl} {$device->port}";
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
