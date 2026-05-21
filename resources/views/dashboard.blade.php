@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<style>
    .dash-card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
    }

    .metric-card {
        border-left: 4px solid var(--metric-color);
        min-height: 116px;
    }

    .metric-icon {
        align-items: center;
        background: rgba(0, 123, 255, 0.08);
        border-radius: 8px;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .metric-value {
        color: #111827;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }

    .status-tile {
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 12px;
    }

    .status-dot {
        border-radius: 50%;
        display: inline-block;
        height: 9px;
        width: 9px;
    }

    .class-progress {
        height: 7px;
    }

    .table thead th {
        border-top: 0;
        color: #6b7280;
        font-size: 12px;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .blink {
        animation: blinker 1.5s linear infinite;
    }

    @keyframes blinker {
        50% { opacity: 0.35; }
    }
</style>

@if($isHoliday)
    <div class="alert alert-info dash-card">
        <i class="fas fa-calendar-day mr-2"></i>
        Today is marked as a holiday: <strong>{{ $isHoliday->title }}</strong>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 font-weight-bold">Today Overview</h5>
                <div class="text-muted">{{ now()->format('l, d M Y') }}</div>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chart-pie mr-1"></i> Open Reports
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card dash-card metric-card" style="--metric-color: #28a745;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted font-weight-bold small">ATTENDANCE RATE</div>
                        <div class="metric-value mt-2">{{ $attendanceRate }}%</div>
                    </div>
                    <span class="metric-icon text-success"><i class="fas fa-user-check"></i></span>
                </div>
                <div class="text-muted small mt-3">{{ $presentLikeToday }} of {{ $totalTrackedUsers }} tracked users counted</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card dash-card metric-card" style="--metric-color: #17a2b8;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted font-weight-bold small">RECORDED TODAY</div>
                        <div class="metric-value mt-2">{{ $recordedToday }}</div>
                    </div>
                    <span class="metric-icon text-info"><i class="fas fa-fingerprint"></i></span>
                </div>
                <div class="progress class-progress mt-3">
                    <div class="progress-bar bg-info" style="width: {{ $recordedRate }}%"></div>
                </div>
                <div class="text-muted small mt-2">{{ $recordedRate }}% scan coverage</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card dash-card metric-card" style="--metric-color: #ffc107;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted font-weight-bold small">LATE / MISSING</div>
                        <div class="metric-value mt-2">{{ $stats['late'] + $stats['missing'] }}</div>
                    </div>
                    <span class="metric-icon text-warning"><i class="fas fa-clock"></i></span>
                </div>
                <div class="text-muted small mt-3">Late: {{ $stats['late'] }} · Missing punch: {{ $stats['missing'] }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card dash-card metric-card" style="--metric-color: #dc3545;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted font-weight-bold small">ABSENT / PENDING</div>
                        <div class="metric-value mt-2">{{ $stats['absent'] + $pendingToday }}</div>
                    </div>
                    <span class="metric-icon text-danger"><i class="fas fa-user-times"></i></span>
                </div>
                <div class="text-muted small mt-3">Absent: {{ $stats['absent'] }} · Pending: {{ $pendingToday }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card dash-card mb-4">
            <div class="card-header bg-white border-0">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-chart-line text-primary mr-2"></i>7 Day Attendance Trend</h3>
            </div>
            <div class="card-body">
                <div style="height: 315px;">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card dash-card mb-4">
            <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-school text-info mr-2"></i>Class Wise Today</h3>
                <span class="badge badge-light">Top {{ $classSummaries->count() }} classes</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Students</th>
                                <th>Present</th>
                                <th>Late</th>
                                <th>Absent</th>
                                <th style="width: 180px;">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classSummaries as $classSummary)
                                <tr>
                                    <td class="font-weight-bold">{{ $classSummary['name'] }}</td>
                                    <td>{{ $classSummary['students'] }}</td>
                                    <td>{{ $classSummary['present'] }}</td>
                                    <td>{{ $classSummary['late'] }}</td>
                                    <td>{{ $classSummary['absent'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress class-progress flex-grow-1 mr-2">
                                                <div class="progress-bar bg-success" style="width: {{ $classSummary['rate'] }}%"></div>
                                            </div>
                                            <span class="small font-weight-bold">{{ $classSummary['rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No class data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card dash-card">
            <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-list-ul text-warning mr-2"></i>Recent Device Scans</h3>
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ optional($activity->user)->name ?? 'Unknown User' }}</div>
                                        <small class="text-muted">ID: {{ optional($activity->user)->device_user_id ?? '-' }}</small>
                                    </td>
                                    <td><span class="badge badge-light text-uppercase">{{ optional($activity->user)->role ?? '-' }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($activity->date)->format('d M') }}</td>
                                    <td>{{ $activity->check_in ?? '-' }}</td>
                                    <td>{{ $activity->check_out ?? '-' }}</td>
                                    <td><span class="badge badge-{{ $activity->status === 'Present' ? 'success' : ($activity->status === 'Absent' ? 'danger' : 'warning') }}">{{ $activity->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No attendance scans found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card dash-card mb-4">
            <div class="card-header bg-white border-0">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-layer-group text-primary mr-2"></i>Status Breakdown</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($statusBreakdown as $item)
                        <div class="col-6 mb-3">
                            <div class="status-tile">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="status-dot bg-{{ $item['class'] }}"></span>
                                    <i class="fas {{ $item['icon'] }} text-{{ $item['class'] }}"></i>
                                </div>
                                <div class="h4 mb-0 mt-2 font-weight-bold">{{ $item['count'] }}</div>
                                <div class="text-muted small">{{ $item['label'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card dash-card mb-4">
            <div class="card-header bg-white border-0">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-heartbeat text-danger mr-2"></i>System Health</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Active Devices</span>
                    <strong>{{ $activeDevices }}/{{ $deviceCount }}</strong>
                </div>
                <div class="progress class-progress mb-4">
                    <div class="progress-bar bg-success" style="width: {{ $deviceCount > 0 ? round(($activeDevices / $deviceCount) * 100) : 0 }}%"></div>
                </div>

                <div class="border rounded p-3 mb-3">
                    <div class="text-muted small font-weight-bold">LAST SYNC</div>
                    <div class="mt-1">
                        <i class="fas fa-circle text-success mr-2 blink" style="font-size: 8px;"></i>
                        <strong>{{ $lastSync && $lastSync->last_sync_time ? $lastSync->last_sync_time->diffForHumans() : 'No sync data' }}</strong>
                    </div>
                    @if($lastSync)
                        <div class="text-muted small mt-2">
                            Processed: {{ $lastSync->processed_records ?? 0 }} · Duplicate: {{ $lastSync->duplicate_records ?? 0 }} · Failed: {{ $lastSync->failed_records ?? 0 }}
                        </div>
                    @endif
                </div>

                <button id="sync-btn" class="btn btn-primary btn-sm btn-block">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Device Data
                </button>
            </div>
        </div>

        <div class="card dash-card">
            <div class="card-header bg-white border-0">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-users text-info mr-2"></i>User Composition</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span><i class="fas fa-graduation-cap text-primary mr-2"></i>Students</span>
                    <strong>{{ $totalStudents }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span><i class="fas fa-chalkboard-teacher text-info mr-2"></i>Teachers</span>
                    <strong>{{ $totalTeachers }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span><i class="fas fa-user-cog text-secondary mr-2"></i>Staff</span>
                    <strong>{{ $totalStaff }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold">Total Users</span>
                    <strong>{{ $totalUsers }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendDates) !!},
                datasets: [
                    {
                        label: 'Present',
                        data: {!! json_encode($trendData) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.10)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Late',
                        data: {!! json_encode($lateTrendData) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.08)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: { grid: { display: false } }
                }
            }
        });

        const syncButton = document.getElementById('sync-btn');
        if (syncButton) {
            syncButton.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...';
                this.disabled = true;

                fetch('{{ route("attendances.sync_trigger") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sync Completed',
                        text: data.message || 'Device data refreshed.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sync Failed',
                        text: 'Please check the device connection.'
                    });
                    this.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Refresh Device Data';
                    this.disabled = false;
                });
            });
        }
    });
</script>
@endsection
