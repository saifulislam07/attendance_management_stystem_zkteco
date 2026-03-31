@extends('layouts.admin')

@section('title', 'Apply for Leave')
@section('page_title', 'Create Leave Application')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Leave Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('leaves.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">User <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id || (auth()->user()->id == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type">Leave Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="Casual Leave">Casual Leave</option>
                                    <option value="Sick Leave">Sick Leave</option>
                                    <option value="Earned Leave">Earned Leave</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason / Description</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Reason for leave...">{{ old('reason') }}</textarea>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                        <a href="{{ route('leaves.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
