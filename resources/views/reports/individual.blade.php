@extends('layouts.admin')

@section('title', 'Individual Attendance History')
@section('page_title', 'Attendance History: ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter History</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.individual', $user->id) }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-info btn-block">Filter History</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Summary Dashboard --}}
<div class="row">
    <div class="col-lg-2 col-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['present'] }}</h3>
                <p>Present</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['late'] }}</h3>
                <p>Late</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['absent'] }}</h3>
                <p>Absent</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['half_day'] }}</h3>
                <p>Half Day</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4">
        <div class="small-box bg-dark">
            <div class="inner">
                <h3>{{ $stats['missing'] }}</h3>
                <p>Missing</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['leave'] }}</h3>
                <p>Leave</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detailed Logs for {{ $user->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('reports.individual.export', [$user->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-default btn-sm"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Early Leave</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
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
                            <td>
                                @if($attendance->early_leave)
                                    <span class="badge badge-warning">Yes</span>
                                @else
                                    <span class="badge badge-success">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No history found for selected range.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
