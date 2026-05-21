@extends('layouts.admin')

@section('title', 'User Management')
@section('page_title', 'Users')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Users</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Search</label>
                            <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Name, email, phone, device ID">
                        </div>
                        <div class="col-md-3">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    @continue($role->name === 'student')
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <a href="{{ route('users.index') }}" class="btn btn-default btn-block">Reset</a>
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
                <h3 class="card-title">User List</h3>
                <div class="card-tools">
                    <button id="bulk-delete-btn" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('users.create', ['role' => 'teacher']) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" id="users-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>Device ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Email</th>
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
                            <td><code>{{ $user->device_user_id }}</code></td>
                            <td>{{ $user->name }}</td>
                            <td>
                                @foreach($user->roles as $userRole)
                                    <span class="badge {{ $userRole->name == 'admin' ? 'badge-danger' : ($userRole->name == 'teacher' ? 'badge-primary' : 'badge-info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $userRole->name)) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>{{ $user->phone ?? '--' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(auth()->user()->id !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn delete-confirm" title="Delete" aria-label="Delete" data-message="All attendance history for this user will be removed.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer table-list-footer">
                <div class="pagination-wrap">
                    @include('partials.per-page')
                </div>
                <div class="pagination-wrap">
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
            text: `You want to delete ${ids.length} selected users? This will also remove their attendance history.`,
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
