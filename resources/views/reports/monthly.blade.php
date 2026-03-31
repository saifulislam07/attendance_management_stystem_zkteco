@extends('layouts.admin')

@section('title', 'Monthly Attendance Summary')
@section('page_title', 'Monthly Overview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Monthly Summary</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.monthly') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Month</label>
                            <select name="month" class="form-control">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Year</label>
                            <select name="year" class="form-control">
                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Generate Monthly Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right mb-3">
        <a href="{{ route('reports.monthly.export', request()->query()) }}" class="btn btn-success mr-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn btn-info"><i class="fas fa-print"></i> Print View</button>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Summary Table</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Student / Teacher</th>
                            <th>Class / Section</th>
                            <th>Logs</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Absent</th>
                            <th>Half Day</th>
                            <th>Missing</th>
                            <th>Leave</th>
                            <th>Growth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="text-left">{{ $user->name }}</td>
                            <td>
                                @if($user->role == 'student')
                                    {{ $user->schoolClass->name ?? '--' }} - {{ $user->section->name ?? '--' }}
                                @else
                                    <span class="badge badge-primary">Teacher</span>
                                @endif
                            </td>
                            <td>{{ $user->monthly_stats->total_days }}</td>
                            <td>{{ $user->monthly_stats->present }}</td>
                            <td>{{ $user->monthly_stats->late }}</td>
                            <td>{{ $user->monthly_stats->absent }}</td>
                            <td>{{ $user->monthly_stats->half_day }}</td>
                            <td>{{ $user->monthly_stats->missing }}</td>
                            <td>{{ $user->monthly_stats->leave }}</td>
                            <td>
                                @php
                                    $percent = $user->monthly_stats->total_days > 0 
                                               ? round(($user->monthly_stats->present / $user->monthly_stats->total_days) * 100) 
                                               : 0;
                                @endphp
                                <div class="progress progress-xs">
                                    <div class="progress-bar {{ $percent > 80 ? 'bg-success' : 'bg-danger' }}" style="width: {{ $percent }}%"></div>
                                </div>
                                <small>{{ $percent }}%</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No summary data found for selected month.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
