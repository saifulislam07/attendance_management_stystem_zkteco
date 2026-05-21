@extends('layouts.admin')

@section('title', 'Timetable Management')
@section('page_title', 'Weekly Timetables')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Timetable Configuration</h3>
                <div class="card-tools">
                    <a href="{{ route('timetables.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Timetable
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('partials.table-search', ['placeholder' => 'Search role, class, day, time...'])
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Class</th>
                            <th>Day</th>
                            <th>In Time</th>
                            <th>Late After</th>
                            <th>Out Time</th>
                            <th>Grace (Min)</th>
                            <th>Half Day After</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $timetable)
                        <tr>
                            <td>
                                <span class="badge {{ $timetable->role == 'teacher' ? 'badge-primary' : 'badge-info' }}">
                                    {{ ucfirst($timetable->role) }}
                                </span>
                            </td>
                            <td>{{ $timetable->schoolClass->name ?? 'Global' }}</td>
                            <td>{{ $timetable->day }}</td>
                            <td>{{ \Carbon\Carbon::parse($timetable->in_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($timetable->late_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($timetable->out_time)->format('h:i A') }}</td>
                            <td>{{ $timetable->grace_time ?? 0 }}</td>
                            <td>{{ $timetable->half_day_time ? \Carbon\Carbon::parse($timetable->half_day_time)->format('h:i A') : '--' }}</td>
                            <td>
                                <a href="{{ route('timetables.edit', $timetable->id) }}" class="btn btn-sm btn-info action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('timetables.destroy', $timetable->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger action-btn" title="Delete" aria-label="Delete" onclick="return confirm('Delete this timetable?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No timetables defined yet.</td>
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
                    {{ $timetables->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
