@extends('layouts.admin')

@section('title', 'Leave Management')
@section('page_title', 'Leave Applications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">All Leave Requests</h3>
                <div class="card-tools">
                    <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Apply for Leave
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('partials.table-search', ['placeholder' => 'Search user, type, status, reason...'])
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Dates</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $leave->user->name }} ({{ ucfirst($leave->user->role) }})</td>
                            <td>{{ $leave->type }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                            </td>
                            <td>{{ Str::limit($leave->reason, 20) }}</td>
                            <td>
                                <span class="badge 
                                    {{ $leave->status == 'approved' ? 'badge-success' : 
                                       ($leave->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td>
                                @if($leave->status == 'pending' && auth()->user()->role == 'admin')
                                    <a href="{{ route('leaves.edit', $leave->id) }}" class="btn btn-info btn-sm action-btn" title="Review" aria-label="Review">
                                        <i class="fas fa-clipboard-check"></i>
                                    </a>
                                @endif
                                <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn" title="Delete" aria-label="Delete" onclick="return confirm('Cancel/Delete this application?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No leave applications found.</td>
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
                    {{ $leaves->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
