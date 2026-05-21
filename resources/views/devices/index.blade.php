@extends('layouts.admin')

@section('title', 'Device Management')
@section('page_title', 'Biometric Devices')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Device List</h3>
                <div class="card-tools">
                    <a href="{{ route('devices.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Device
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('partials.table-search', ['placeholder' => 'Search device, IP, port, location...'])
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>IP Address</th>
                            <th>Port</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Last Online</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td>{{ $device->id }}</td>
                            <td>{{ $device->name }}</td>
                            <td>{{ $device->ip_address }}</td>
                            <td>{{ $device->port }}</td>
                            <td>{{ $device->location }}</td>
                            <td>
                                <span class="badge {{ $device->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $device->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $device->last_online_at ? $device->last_online_at->format('d M Y, h:i A') : 'Never' }}</td>
                            <td>
                                <a href="{{ route('devices.edit', $device) }}" class="btn btn-info btn-sm action-btn" title="Edit" aria-label="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn" title="Delete" aria-label="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No devices found.</td>
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
                    {{ $devices->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
