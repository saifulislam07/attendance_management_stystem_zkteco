@extends('layouts.admin')

@section('title', 'Student List')
@section('page_title', 'Students')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Students</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('students.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Class</label>
                            <select name="class_id" class="form-control">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Section</label>
                            <select name="section_id" class="form-control">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Shift</label>
                            <select name="shift" class="form-control">
                                <option value="">All Shifts</option>
                                <option value="Morning" {{ request('shift') == 'Morning' ? 'selected' : '' }}>Morning</option>
                                <option value="Day" {{ request('shift') == 'Day' ? 'selected' : '' }}>Day</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student List</h3>
                <div class="card-tools">
                    <button id="bulk-delete-btn" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('students.import') }}" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-file-import"></i> Import Students
                    </a>
                    <a href="{{ route('students.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add Student
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" id="students-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>Photo</th>
                            <th>Roll No</th>
                            <th>Admission No</th>
                            <th>Device ID</th>
                            <th>Name</th>
                            <th>Class / Section</th>
                            <th>Shift</th>
                            <th>Guardian</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr id="user-row-{{ $user->id }}">
                            <td>
                                @if(auth()->id() !== $user->id)
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                @endif
                            </td>
                            <td>
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="img-circle" style="width: 38px; height: 38px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $user->roll_no ?? '--' }}</span></td>
                            <td>{{ $user->admission_no ?? '--' }}</td>
                            <td><code>{{ $user->device_user_id }}</code></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->schoolClass->name ?? '--' }} / {{ $user->section->name ?? '--' }}</td>
                            <td>{{ $user->shift ?? '--' }}</td>
                            <td>
                                {{ $user->guardian_name ?? '--' }}
                                @if($user->guardian_relation)
                                    <small class="text-muted d-block">{{ $user->guardian_relation }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $user->guardian_phone ?? '--' }}
                                @if($user->guardian_email)
                                    <small class="text-muted d-block">{{ $user->guardian_email }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('students.show', $user->id) }}" class="btn btn-primary btn-sm action-btn" title="View" aria-label="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $user->id) }}" class="btn btn-info btn-sm action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(auth()->user()->id !== $user->id)
                                <form action="{{ route('students.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn delete-confirm" title="Delete" aria-label="Delete" data-message="All attendance history for this student will be removed.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No students found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer table-list-footer">
                <div>
                    @include('partials.per-page')
                </div>
                <div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const selectAll = $('#select-all');
    const checkboxes = $('.user-checkbox');
    const bulkBtn = $('#bulk-delete-btn');

    function toggleBulkBtn() {
        const checkedCount = $('.user-checkbox:checked').length;
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
        $('.user-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete ${ids.length} selected students? This will also remove their attendance history.`,
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
                    url: "{{ route('users.bulk_delete') }}",
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
