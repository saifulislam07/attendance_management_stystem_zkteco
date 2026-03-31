<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\SyncLog;
use App\Models\Device;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $isHoliday = Holiday::where('date', $today)->first();
        
        $attendances = Attendance::where('date', $today)->get();
        
        $stats = [
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'missing' => $attendances->where('status', 'Missing Punch')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
        ];

        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStaff = User::whereNotIn('role', ['student', 'teacher', 'admin'])->count();
        
        $deviceCount = Device::count();
        $activeDevices = Device::where('status', true)->count();
        
        $lastSync = SyncLog::orderBy('created_at', 'desc')->first();

        // 7 Days Attendance Trend
        $trendDates = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $label = Carbon::today()->subDays($i)->format('D, M d');
            $count = Attendance::where('date', $date)->where('status', 'Present')->count();
            
            $trendDates[] = $label;
            $trendData[] = $count;
        }

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
            'totalStaff',
            'lastSync', 
            'deviceCount', 
            'activeDevices', 
            'isHoliday',
            'trendDates',
            'trendData',
            'recentActivities'
        ));
    }
}
