@extends('layouts.admin')

@section('title', 'Daily Attendance Reports')
@section('page_title', 'Attendance Reports')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Reports</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-2">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Class</label>
                            <select name="class_id" class="form-control">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Present</span>
                <span class="info-box-number">{{ $stats['present'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Late</span>
                <span class="info-box-number">{{ $stats['late'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-adjust"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Half Day</span>
                <span class="info-box-number">{{ $stats['half_day'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-fingerprint"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Missing</span>
                <span class="info-box-number">{{ $stats['missing'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3 bg-danger">
            <div class="info-box-content">
                <span class="info-box-text">Absent</span>
                <span class="info-box-number">{{ $stats['absent'] }}</span>
                @if($stats['absent'] > 0)
                <button onclick="alert('Mock: Sending SMS to guardians of absent students...')" class="btn btn-xs btn-outline-light mt-1">Send Alerts</button>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-2">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-sign-out-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Leave</span>
                <span class="info-box-number">{{ $stats['leave'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daily Report Results</h3>
                <div class="card-tools">
                    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-default btn-sm"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Class / Section</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date }}</td>
                            <td>
                                <a href="{{ route('reports.individual', $attendance->user->id) }}">
                                    {{ $attendance->user->name }}
                                </a>
                            </td>
                            <td>{{ ucfirst($attendance->user->role) }}</td>
                            <td>{{ $attendance->user->schoolClass->name ?? '--' }} / {{ $attendance->user->section->name ?? '--' }}</td>
                            <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--' }}</td>
                            <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $attendance->status == 'Present' ? 'badge-success' : 
                                       ($attendance->status == 'Late' ? 'badge-warning' : 
                                       ($attendance->status == 'Half Day' ? 'badge-info' : 
                                       ($attendance->status == 'Missing Punch' ? 'badge-dark' : 'badge-danger'))) }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No logs found for the selected range.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
