@extends('layouts.admin')

@section('title', 'Edit Device')
@section('page_title', 'Edit Biometric Device')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Device Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('devices.update', $device) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $device->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_address">IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control" value="{{ $device->ip_address }}" required>
                    </div>
                    <div class="form-group">
                        <label for="port">Port <span class="text-danger">*</span></label>
                        <input type="number" name="port" id="port" class="form-control" value="{{ $device->port }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $device->location }}">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ $device->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$device->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Device</button>
                        <a href="{{ route('devices.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
