@extends('layouts.admin')

@section('title', 'Add New Device')
@section('page_title', 'Add Biometric Device')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Device Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('devices.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Office Main Gate" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_address">IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control" placeholder="192.168.1.201" required>
                    </div>
                    <div class="form-group">
                        <label for="port">Port <span class="text-danger">*</span></label>
                        <input type="number" name="port" id="port" class="form-control" value="4370" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" placeholder="Main Entrance">
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Device</button>
                        <a href="{{ route('devices.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
