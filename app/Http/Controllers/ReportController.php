<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Holiday;
use App\Models\Leave;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    /**
     * Daily / Range Attendance Logs with Stats
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = $this->buildReportRows($request, $startDate, $endDate, 'desc');
        $classes = SchoolClass::all();

        $stats = $this->statusSummary($attendances);

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
        
        $this->applyUserFilters($query, $request);

        $users = $query->get();
        
        foreach ($users as $user) {
            $user->monthly_stats = $this->monthlyStatsForUser($user, $month, $year);
        }

        $classes = SchoolClass::all();
        
        return view('reports.monthly', compact('users', 'classes', 'month', 'year'));
    }

    /**
     * Individual User History with Summary
     */
    public function individual(Request $request, User $user)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = $this->buildUserReportRows($user, $startDate, $endDate, 'desc');

        $stats = $this->statusSummary($attendances);
            
        return view('reports.individual', compact('user', 'attendances', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Export Daily Logs to CSV (Excel Friendly)
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = $this->buildReportRows($request, $startDate, $endDate, 'asc');

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
                    $row->user?->name ?? 'Unknown User',
                    ucfirst($row->user->role ?? '--'),
                    $row->user?->schoolClass?->name ?? '--',
                    $row->user?->section?->name ?? '--',
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
        
        $this->applyUserFilters($query, $request);

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
                $stats = $this->monthlyStatsForUser($user, $month, $year);

                fputcsv($file, [
                    $user->name,
                    ucfirst($user->role ?? '--'),
                    ($user->schoolClass->name ?? '--') . ' / ' . ($user->section->name ?? '--'),
                    $stats->total_days,
                    $stats->present,
                    $stats->late,
                    $stats->absent,
                    $stats->half_day,
                    $stats->missing,
                    $stats->leave,
                    $stats->attendance_percent . '%'
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
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        $attendances = $this->buildUserReportRows($user, $startDate, $endDate, 'asc');

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

    private function applyUserFilters($query, Request $request): void
    {
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        } else {
            $query->whereIn('role', ['student', 'teacher']);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('device_user_id', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%")
                    ->orWhere('roll_no', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('schoolClass', fn ($classQuery) => $classQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('section', fn ($sectionQuery) => $sectionQuery->where('name', 'like', "%{$search}%"));
            });
        }
    }

    private function statusSummary($attendances): array
    {
        $presentLike = $attendances->whereIn('status', ['Present', 'Late', 'Half Day', 'Missing Punch'])->count();
        $total = $attendances->count();

        return [
            'total' => $total,
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'missing' => $attendances->where('status', 'Missing Punch')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
            'present_like' => $presentLike,
            'attendance_percent' => $total > 0 ? round(($presentLike / $total) * 100) : 0,
        ];
    }

    private function monthlyStatsForUser(User $user, string $month, string $year): object
    {
        $startDate = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        if ($startDate->isSameMonth(Carbon::today())) {
            $endDate = Carbon::today();
        }

        $summary = $this->statusSummary($this->buildUserReportRows(
            $user,
            $startDate->toDateString(),
            $endDate->toDateString(),
            'asc'
        ));

        $stats = (object) [
            'total_days' => $summary['total'],
            'present' => $summary['present'],
            'late' => $summary['late'],
            'absent' => $summary['absent'],
            'half_day' => $summary['half_day'],
            'missing' => $summary['missing'],
            'leave' => $summary['leave'],
            'present_like' => $summary['present_like'],
            'attendance_percent' => $summary['attendance_percent'],
        ];

        return $stats;
    }

    private function buildReportRows(Request $request, string $startDate, string $endDate, string $direction = 'desc'): Collection
    {
        $usersQuery = User::with(['schoolClass', 'section'])->orderBy('name');
        $this->applyUserFilters($usersQuery, $request);

        $users = $usersQuery->get();
        $actualAttendances = Attendance::with(['user.schoolClass', 'user.section'])
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->keyBy(fn ($attendance) => $attendance->user_id . '|' . $attendance->date);

        return $this->buildRowsForUsers($users, $actualAttendances, $startDate, $endDate, $direction);
    }

    private function buildUserReportRows(User $user, string $startDate, string $endDate, string $direction = 'desc'): Collection
    {
        $user->loadMissing(['schoolClass', 'section']);

        $actualAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn ($attendance) => $attendance->user_id . '|' . $attendance->date);

        return $this->buildRowsForUsers(collect([$user]), $actualAttendances, $startDate, $endDate, $direction);
    }

    private function buildRowsForUsers(Collection $users, Collection $actualAttendances, string $startDate, string $endDate, string $direction): Collection
    {
        $dates = collect(CarbonPeriod::create($startDate, $endDate))
            ->map(fn (Carbon $date) => $date->toDateString());
        $holidayDates = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->all();
        $rows = collect();

        foreach ($dates as $date) {
            if (in_array($date, $holidayDates, true)) {
                continue;
            }

            foreach ($users as $user) {
                $key = $user->id . '|' . $date;
                $attendance = $actualAttendances->get($key);

                if ($attendance) {
                    $attendance->setRelation('user', $user);
                    $rows->push($attendance);
                    continue;
                }

                $rows->push((object) [
                    'user_id' => $user->id,
                    'date' => $date,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => $this->leaveStatusForDate($user, $date) ?: 'Absent',
                    'early_leave' => false,
                    'user' => $user,
                ]);
            }
        }

        return $rows
            ->sortBy([
                ['date', $direction],
                fn ($a, $b) => strcmp($a->user->name ?? '', $b->user->name ?? ''),
            ])
            ->values();
    }

    private function leaveStatusForDate(User $user, string $date): ?string
    {
        return Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists() ? 'Leave' : null;
    }
}
