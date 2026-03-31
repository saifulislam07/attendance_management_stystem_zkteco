@extends('layouts.admin')

@section('title', 'Holiday Management')
@section('page_title', 'Holidays')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">School Holidays List</h3>
                <div class="card-tools">
                    <a href="{{ route('holidays.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Holiday
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Holiday Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</td>
                            <td>{{ $holiday->title }}</td>
                            <td>
                                <a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-info btn-sm">Edit</a>
                                <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove this holiday?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No holidays defined yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $holidays->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
