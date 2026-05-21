<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\SyncLog;
use App\Models\Device;
use App\Models\Holiday;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $isHoliday = Holiday::where('date', $today)->first();
        
        $attendances = Attendance::with('user')->where('date', $today)->get();
        
        $stats = [
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'missing' => $attendances->where('status', 'Missing Punch')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
        ];

        $totalUsers = User::count();
        $totalTrackedUsers = User::whereIn('role', ['student', 'teacher'])->count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStaff = User::whereNotIn('role', ['student', 'teacher', 'admin'])->count();
        $recordedToday = $attendances->pluck('user_id')->unique()->count();
        $presentLikeToday = $stats['present'] + $stats['late'] + $stats['half_day'] + $stats['missing'];
        $pendingToday = max($totalTrackedUsers - $recordedToday, 0);
        $attendanceRate = $totalTrackedUsers > 0 ? round(($presentLikeToday / $totalTrackedUsers) * 100, 1) : 0;
        $recordedRate = $totalTrackedUsers > 0 ? round(($recordedToday / $totalTrackedUsers) * 100, 1) : 0;
        
        $deviceCount = Device::count();
        $activeDevices = Device::where('status', true)->count();
        
        $lastSync = SyncLog::orderBy('created_at', 'desc')->first();

        $statusBreakdown = [
            ['label' => 'Present', 'count' => $stats['present'], 'class' => 'success', 'icon' => 'fa-user-check'],
            ['label' => 'Late', 'count' => $stats['late'], 'class' => 'warning', 'icon' => 'fa-clock'],
            ['label' => 'Absent', 'count' => $stats['absent'], 'class' => 'danger', 'icon' => 'fa-user-times'],
            ['label' => 'Half Day', 'count' => $stats['half_day'], 'class' => 'info', 'icon' => 'fa-adjust'],
            ['label' => 'Missing Punch', 'count' => $stats['missing'], 'class' => 'secondary', 'icon' => 'fa-exclamation-circle'],
            ['label' => 'Leave', 'count' => $stats['leave'], 'class' => 'primary', 'icon' => 'fa-calendar-minus'],
            ['label' => 'Pending', 'count' => $pendingToday, 'class' => 'dark', 'icon' => 'fa-hourglass-half'],
        ];

        // 7 Days Attendance Trend
        $trendDates = [];
        $trendData = [];
        $lateTrendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $label = Carbon::today()->subDays($i)->format('D, M d');
            $count = Attendance::where('date', $date)->where('status', 'Present')->count();
            $lateCount = Attendance::where('date', $date)->where('status', 'Late')->count();
            
            $trendDates[] = $label;
            $trendData[] = $count;
            $lateTrendData[] = $lateCount;
        }

        $classSummaries = SchoolClass::withCount(['users as student_count' => function ($query) {
                $query->where('role', 'student');
            }])
            ->with(['users' => function ($query) use ($today) {
                $query->where('role', 'student')
                    ->with(['attendances' => fn ($attendanceQuery) => $attendanceQuery->where('date', $today)]);
            }])
            ->orderBy('name')
            ->get()
            ->map(function (SchoolClass $class) {
                $todayAttendances = $class->users
                    ->flatMap(fn (User $user) => $user->attendances)
                    ->values();
                $presentCount = $todayAttendances
                    ->whereIn('status', ['Present', 'Late', 'Half Day', 'Missing Punch'])
                    ->count();
                $rate = $class->student_count > 0 ? round(($presentCount / $class->student_count) * 100) : 0;

                return [
                    'name' => $class->name,
                    'students' => $class->student_count,
                    'present' => $presentCount,
                    'late' => $todayAttendances->where('status', 'Late')->count(),
                    'absent' => $todayAttendances->where('status', 'Absent')->count(),
                    'rate' => $rate,
                ];
            })
            ->take(8);

        // Recent Activity (Latest 10 punches)
        $recentActivities = Attendance::with('user')
            ->whereNotNull('check_in')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'totalStudents', 
            'totalTeachers', 
            'totalUsers', 
            'totalTrackedUsers',
            'totalStaff',
            'recordedToday',
            'presentLikeToday',
            'pendingToday',
            'attendanceRate',
            'recordedRate',
            'statusBreakdown',
            'lastSync', 
            'deviceCount', 
            'activeDevices', 
            'isHoliday',
            'trendDates',
            'trendData',
            'lateTrendData',
            'classSummaries',
            'recentActivities'
        ));
    }
}
