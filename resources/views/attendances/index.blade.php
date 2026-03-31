@extends('layouts.admin')

@section('title', 'Attendance Logs')
@section('page_title', 'Attendance Logs')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Logs</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('attendances.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
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
                            <label>Section</label>
                            <select name="section_id" class="form-control">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter Logs</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Attendance Results</h3>
                <div class="card-tools">
                    <button id="bulk-delete-btn" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('attendances.create') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-plus"></i> Manual Attendance
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" id="attendance-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Class / Section</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Early Leave</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td><input type="checkbox" class="log-checkbox" value="{{ $attendance->id }}"></td>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                            <td>{{ $attendance->user->name }}</td>
                            <td>
                                @foreach($attendance->user->roles as $userRole)
                                    <span class="badge badge-secondary">{{ ucfirst($userRole->name) }}</span>
                                @endforeach
                            </td>
                            <td>
                                {{ $attendance->user->schoolClass->name ?? '--' }} / 
                                {{ $attendance->user->section->name ?? '--' }}
                            </td>
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
                            <td colspan="9" class="text-center text-muted">No attendance records found for selected filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const selectAll = $('#select-all');
    const checkboxes = $('.log-checkbox');
    const bulkBtn = $('#bulk-delete-btn');

    function toggleBulkBtn() {
        const checkedCount = $('.log-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkBtn.fadeIn();
            bulkBtn.html(`<i class="fas fa-trash"></i> Delete Selected (${checkedCount})`);
        } else {
            bulkBtn.fadeOut();
        }
    }

    selectAll.on('change', function() {
        checkboxes.prop('checked', $(this).prop('checked'));
        toggleBulkBtn();
    });

    checkboxes.on('change', function() {
        if (!$(this).prop('checked')) {
            selectAll.prop('checked', false);
        }
        toggleBulkBtn();
    });

    bulkBtn.on('click', function() {
        const ids = [];
        $('.log-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete ${ids.length} selected logs? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                bulkBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: "{{ route('attendances.bulk_delete') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            bulkBtn.prop('disabled', false).html(`<i class="fas fa-trash"></i> Delete Selected (${ids.length})`);
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        bulkBtn.prop('disabled', false).html(`<i class="fas fa-trash"></i> Delete Selected (${ids.length})`);
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
