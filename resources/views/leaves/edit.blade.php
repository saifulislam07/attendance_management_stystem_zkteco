@extends('layouts.admin')

@section('title', 'Review Leave')
@section('page_title', 'Update Leave Status')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Leave Details</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>User:</label>
                    <p class="form-control-static">{{ $leave->user->name }} ({{ ucfirst($leave->user->role) }})</p>
                </div>
                <div class="form-group">
                    <label>Type:</label>
                    <p class="form-control-static">{{ $leave->type }}</p>
                </div>
                <div class="form-group">
                    <label>Duration:</label>
                    <p class="form-control-static">
                        {{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}
                    </p>
                </div>
                <div class="form-group">
                    <label>Reason:</label>
                    <p class="form-control-static">{{ $leave->reason ?? '--' }}</p>
                </div>

                <hr>

                <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="status">Action <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" {{ $leave->status == 'pending' ? 'selected' : '' }}>Keep Pending</option>
                            <option value="approved" {{ $leave->status == 'approved' ? 'selected' : '' }}>Approve</option>
                            <option value="rejected" {{ $leave->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">Update Status</button>
                        <a href="{{ route('leaves.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
