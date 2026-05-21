@extends('layouts.admin')

@section('title', 'Section Management')
@section('page_title', 'All Sections')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Section List</h3>
                <div class="card-tools">
                    <button id="bulk-delete-btn" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('sections.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add Section
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('partials.table-search', ['placeholder' => 'Search section or class...'])
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Section Name</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <td><input type="checkbox" class="section-checkbox" value="{{ $section->id }}"></td>
                            <td>{{ $section->id }}</td>
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->schoolClass->name ?? '--' }}</td>
                            <td>
                                <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-info btn-sm action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sections.destroy', $section->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn delete-confirm" title="Delete" aria-label="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No sections found.</td>
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
                    {{ $sections->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const selectAll = $('#select-all');
    const checkboxes = $('.section-checkbox');
    const bulkBtn = $('#bulk-delete-btn');

    function toggleBulkBtn() {
        const checkedCount = $('.section-checkbox:checked').length;
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
        $('.section-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete ${ids.length} selected sections? This action cannot be undone.`,
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
                    url: "{{ route('sections.bulk_delete') }}",
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
