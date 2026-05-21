@extends('layouts.admin')

@section('title', 'Manual Attendance')
@section('page_title', 'Add Manual Attendance')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .manual-attendance-page .select2-container {
        display: block;
        width: 100% !important;
    }

    .manual-attendance-page .select2-container .select2-selection--single {
        align-items: center;
        display: flex;
        height: 38px;
        min-height: 38px;
    }

    .select2-container--bootstrap4 .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    .manual-attendance-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
    .manual-attendance-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        align-items: center;
        display: flex;
        height: 100%;
        line-height: 1.4;
        padding-left: .75rem;
        padding-right: 2rem;
        width: 100%;
    }

    .manual-attendance-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow,
    .manual-attendance-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: .35rem;
    }

    .manual-attendance-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder,
    .manual-attendance-page .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .manual-attendance-page .select2-container--open .select2-dropdown {
        border-color: #80bdff;
        width: 100% !important;
    }

    .manual-attendance-page .select2-search--dropdown {
        padding: .5rem;
    }

    .manual-attendance-page .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: .25rem;
        height: 38px;
        line-height: 1.4;
        outline: 0;
        padding: .375rem .75rem;
        width: 100% !important;
    }

    .manual-attendance-page .select2-results__option {
        white-space: normal;
    }

    .manual-attendance-page .card-footer {
        margin-left: -.25rem;
        margin-right: -.25rem;
    }
</style>

<div class="row manual-attendance-page">
    <div class="col-md-8">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> Device fallback entry</h5>
            Use this form when the biometric device, network, or sync script is unavailable. Existing attendance for the same user and date will be updated.
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Manual Entry Form</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('attendances.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">User <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control select2" data-placeholder="Search by name, role, class, or device ID" required>
                            <option value=""></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ ucfirst($user->role) }}
                                    @if($user->role === 'student')
                                        | {{ $user->schoolClass->name ?? 'No Class' }} / {{ $user->section->name ?? 'No Section' }}
                                    @endif
                                    | Device ID: {{ $user->device_user_id ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="check_in">Check In</label>
                                <select name="check_in" id="check_in" class="form-control time-select">
                                    <option value="">-- Select Time --</option>
                                    @foreach($timeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('check_in') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('check_in')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="check_out">Check Out</label>
                                <select name="check_out" id="check_out" class="form-control time-select">
                                    <option value="">-- Select Time --</option>
                                    @foreach($timeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('check_out') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('check_out')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['Present', 'Late', 'Half Day', 'Missing Punch', 'Absent', 'Leave'] as $status)
                                <option value="{{ $status }}" {{ old('status', 'Present') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save Attendance
                        </button>
                        <a href="{{ route('attendances.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Quick Guide</h3>
            </div>
            <div class="card-body">
                <ul class="mb-0 pl-3">
                    <li>Only select <strong>Absent</strong> when no punch was taken.</li>
                    <li>Use <strong>Missing Punch</strong> when only check-in or check-out is known.</li>
                    <li>Use <strong>Leave</strong> for approved leave days.</li>
                    <li>Saving again for the same user/date updates the old entry.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    $('#user_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Search by name, role, class, or device ID',
        allowClear: true
    });

    $('.time-select').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Search time',
        allowClear: true
    });
});
</script>
@endpush
