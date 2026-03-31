@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Smart Management Dashboard')

@section('content')
<!-- Welcome & Quick Stats Section -->
<div class="row mb-4 bounceInDown animated">
    <div class="col-12">
        <h5 class="text-muted font-weight-normal mb-3">Today's Real-time Overview <span class="badge badge-info ml-2 px-2">{{ date('d M Y') }}</span></h5>
    </div>
    
    <!-- Attendance Summary Cards (Redesigned) -->
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-0" style="border-left: 5px solid #28a745 !important; border-radius: 12px;">
            <span class="info-box-icon text-success bg-transparent"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">PRESENT TODAY</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $stats['present'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-0" style="border-left: 5px solid #ffc107 !important; border-radius: 12px;">
            <span class="info-box-icon text-warning bg-transparent"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">LATE ARRIVALS</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $stats['late'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-0" style="border-left: 5px solid #dc3545 !important; border-radius: 12px;">
            <span class="info-box-icon text-danger bg-transparent"><i class="fas fa-user-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">ABSENT TODAY</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $stats['absent'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-0" style="border-left: 5px solid #17a2b8 !important; border-radius: 12px;">
            <span class="info-box-icon text-info bg-transparent"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">TOTAL REGISTERED</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $totalUsers }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="row">
    <!-- Left Column: Trend Analysis -->
    <div class="col-lg-8">
        <!-- Attendance Trend Chart -->
        <div class="card card-white shadow-sm border-0 mb-4" style="border-radius: 16px;">
            <div class="card-header border-0 bg-transparent py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-chart-line text-primary mr-2"></i> Attendance Trend (Last 7 Days)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height:320px; width:100%">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card card-white shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header border-0 bg-transparent py-4">
                <h3 class="card-title font-weight-bold"><i class="fas fa-list-ul text-warning mr-2"></i> Live Activity Feed</h3>
                <div class="card-tools">
                    <span class="badge badge-warning px-2">Most Recent 10 Scans</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Time</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 text-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $activity->user->name }}</div>
                                                <small class="text-muted">ID: {{ $activity->user->device_user_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-light px-2 py-1 text-uppercase" style="font-size: 10px;">{{ $activity->user->role }}</span></td>
                                    <td>{{ $activity->check_in }}</td>
                                    <td><i class="fas fa-fingerprint text-info mr-1"></i> Device</td>
                                    <td>
                                        <span class="dot mr-1 {{ $activity->status == 'Present' ? 'bg-success' : 'bg-warning' }}" style="height: 10px; width: 10px; border-radius: 50%; display: inline-block;"></span>
                                        <span class="font-weight-600">{{ $activity->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No activities recorded today yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-center py-3">
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">View All Logs</a>
            </div>
        </div>
    </div>

    <!-- Right Column: System Health & Roles -->
    <div class="col-lg-4">
        <!-- System Health Section -->
        <div class="card card-white shadow-sm border-0 mb-4" style="border-radius: 16px;">
            <div class="card-header border-0 bg-transparent py-4 text-center">
                <h3 class="card-title w-100 font-weight-bold"><i class="fas fa-heartbeat text-danger mr-2"></i> System Health</h3>
            </div>
            <div class="card-body">
                <!-- Device Status -->
                <div class="text-center mb-4">
                    <div class="display-4 font-weight-bold text-dark mb-0">{{ $activeDevices }}<span class="h5 text-muted">/{{ $deviceCount }}</span></div>
                    <p class="text-muted text-uppercase small font-weight-bold">Active ZKTeco Devices</p>
                    <div class="progress rounded-pill bg-light shadow-none" style="height: 10px;">
                        <div class="progress-bar bg-success rounded-pill" style="width: {{ $deviceCount > 0 ? ($activeDevices/$deviceCount)*100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Last Sync Detail -->
                <div class="bg-light rounded p-3 mb-4 text-center">
                    <small class="text-muted d-block font-weight-bold">STATION DATA SYNC STATUS</small>
                    <div class="mt-1 d-flex justify-content-center align-items-center">
                        <i class="fas fa-circle text-success mr-2 blink" style="font-size: 8px;"></i>
                        <span class="font-weight-bold text-dark">{{ $lastSync ? $lastSync->last_sync_time->diffForHumans() : 'No Sync Data' }}</span>
                    </div>
                    <button id="sync-btn" class="btn btn-primary btn-sm btn-block mt-3 rounded-pill shadow-sm"><i class="fas fa-sync-alt mr-2"></i> Refresh Data Now</button>
                </div>
            </div>
        </div>

        <!-- Role Composition -->
        <div class="card card-white shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header border-0 bg-transparent py-4">
                <h3 class="card-title font-weight-bold"><i class="fas fa-user-tag text-info mr-2"></i> User Composition</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-graduation-cap text-primary mr-3"></i> Students
                        </div>
                        <span class="badge badge-primary px-2 rounded-pill">{{ $totalStudents }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chalkboard-teacher text-info mr-3"></i> Teachers
                        </div>
                        <span class="badge badge-info px-2 rounded-pill">{{ $totalTeachers }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog text-secondary mr-3"></i> Technical Staff
                        </div>
                        <span class="badge badge-secondary px-2 rounded-pill">{{ $totalStaff }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0.3; } }
    .font-weight-600 { font-weight: 600; }
    .card-tools .btn-tool { color: #ccc; }
    .table thead th { border-top: none; }
    .info-box-icon { font-size: 1.5rem !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Trend Chart (Chart.js)
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendDates) !!},
                datasets: [{
                    label: 'Present Students',
                    data: {!! json_encode($trendData) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { stepSize: 10 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Sync Trigger Script
        document.getElementById('sync-btn').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
            this.disabled = true;

            fetch('{{ route("attendances.sync_trigger") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Sync Completed',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: 'Please check your connection to the ZKTeco bridge.'
                });
                this.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Refresh Data Now';
                this.disabled = false;
            });
        });
    });
</script>
@endsection
