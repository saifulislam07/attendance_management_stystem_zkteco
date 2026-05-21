@extends('layouts.admin')

@section('title', 'Class Management')
@section('page_title', 'All Classes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Class List</h3>
                <div class="card-tools">
                    <button id="bulk-delete-btn" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('classes.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add Class
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('partials.table-search', ['placeholder' => 'Search class name...'])
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Class Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                        <tr>
                            <td><input type="checkbox" class="class-checkbox" value="{{ $class->id }}"></td>
                            <td>{{ $class->id }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-info btn-sm action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn delete-confirm" title="Delete" aria-label="Delete" data-message="All sections associated with this class will also be removed.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No classes found.</td>
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
                    {{ $classes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const selectAll = $('#select-all');
    const checkboxes = $('.class-checkbox');
    const bulkBtn = $('#bulk-delete-btn');

    function toggleBulkBtn() {
        const checkedCount = $('.class-checkbox:checked').length;
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
        $('.class-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete ${ids.length} selected classes? This will also delete associated sections.`,
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
                    url: "{{ route('classes.bulk_delete') }}",
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
