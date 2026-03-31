<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Holiday;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Daily / Range Attendance Logs with Stats
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $query = Attendance::with(['user.schoolClass', 'user.section'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();
        $classes = SchoolClass::all();

        // Summary Stats (Filtered)
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'missing' => $attendances->where('status', 'Missing Punch')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
        ];

        return view('reports.index', compact('attendances', 'classes', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Monthly Aggregate Report
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));
        
        $query = User::with(['schoolClass', 'section']);
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $users = $query->get();
        
        foreach ($users as $user) {
            $user->monthly_stats = DB::table('attendances')
                ->where('user_id', $user->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->selectRaw('
                    COUNT(*) as total_days,
                    SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = "Half Day" THEN 1 ELSE 0 END) as half_day,
                    SUM(CASE WHEN status = "Missing Punch" THEN 1 ELSE 0 END) as missing,
                    SUM(CASE WHEN status = "Leave" THEN 1 ELSE 0 END) as leave
                ')->first();
        }

        $classes = SchoolClass::all();
        
        return view('reports.monthly', compact('users', 'classes', 'month', 'year'));
    }

    /**
     * Individual User History with Summary
     */
    public function individual(Request $request, User $user)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Summary Stats (Individual)
        $stats = [
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'missing' => $attendances->where('status', 'Missing Punch')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
        ];
            
        return view('reports.individual', compact('user', 'attendances', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Export Daily Logs to CSV (Excel Friendly)
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $query = Attendance::with(['user.schoolClass', 'user.section'])
            ->whereBetween('date', [$startDate, $endDate]);

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

        $attendances = $query->orderBy('date', 'asc')->get();

        $filename = "attendance_report_{$startDate}_to_{$endDate}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Name', 'Role', 'Class', 'Section', 'Check-in', 'Check-out', 'Status']);

            foreach ($attendances as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->user->name,
                    ucfirst($row->user->roles->first()->name ?? '--'),
                    $row->user->schoolClass->name ?? '--',
                    $row->user->section->name ?? '--',
                    $row->check_in ? Carbon::parse($row->check_in)->format('h:i A') : '--',
                    $row->check_out ? Carbon::parse($row->check_out)->format('h:i A') : '--',
                    $row->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Monthly Summary to CSV
     */
    public function monthlyExport(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));
        
        $query = User::with(['schoolClass', 'section']);
        
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $users = $query->get();
        $filename = "monthly_summary_{$month}_{$year}.csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($users, $month, $year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Role', 'Class/Section', 'Total Logs', 'Present', 'Late', 'Absent', 'Half Day', 'Missing', 'Leave', 'Attendance %']);

            foreach ($users as $user) {
                $stats = DB::table('attendances')
                    ->where('user_id', $user->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->selectRaw('
                        COUNT(*) as total_days,
                        SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent,
                        SUM(CASE WHEN status = "Half Day" THEN 1 ELSE 0 END) as half_day,
                        SUM(CASE WHEN status = "Missing Punch" THEN 1 ELSE 0 END) as missing,
                        SUM(CASE WHEN status = "Leave" THEN 1 ELSE 0 END) as leave
                    ')->first();

                $percent = $stats->total_days > 0 ? round(($stats->present / $stats->total_days) * 100) : 0;

                fputcsv($file, [
                    $user->name,
                    ucfirst($user->roles->first()->name ?? '--'),
                    ($user->schoolClass->name ?? '--') . ' / ' . ($user->section->name ?? '--'),
                    $stats->total_days,
                    $stats->present,
                    $stats->late,
                    $stats->absent,
                    $stats->half_day,
                    $stats->missing,
                    $stats->leave,
                    $percent . '%'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Individual History to CSV
     */
    public function individualExport(Request $request, User $user)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $filename = "history_" . str_replace(' ', '_', strtolower($user->name)) . "_{$startDate}_{$endDate}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($attendances, $user) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Attendance History for: ' . $user->name]);
            fputcsv($file, ['Date', 'Check-in', 'Check-out', 'Status', 'Early Leave']);

            foreach ($attendances as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->check_in ? Carbon::parse($row->check_in)->format('h:i A') : '--',
                    $row->check_out ? Carbon::parse($row->check_out)->format('h:i A') : '--',
                    $row->status,
                    $row->early_leave ? 'Yes' : 'No'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
