@extends('layouts.admin')

@section('title', 'Edit User')
@section('page_title', 'Update User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">User Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ $user->role == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="office_boy" {{ $user->role == 'office_boy' ? 'selected' : '' }}>Office Boy</option>
                                    <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>General Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="device_user_id">Device User ID <span class="text-danger">*</span></label>
                                <input type="text" name="device_user_id" id="device_user_id" class="form-control" value="{{ $user->device_user_id }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}">
                            </div>
                        </div>
                    </div>

                    <div class="row" id="student_fields">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="class_id">Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="class_id" class="form-control">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $user->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="section_id">Section</label>
                                <select name="section_id" id="section_id" class="form-control">
                                    <option value="">Select Section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ $user->section_id == $section->id ? 'selected' : '' }}>{{ $section->name }} ({{ $section->schoolClass->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shift">Shift</label>
                                <select name="shift" id="shift" class="form-control">
                                    <option value="Morning" {{ $user->shift == 'Morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="Day" {{ $user->shift == 'Day' ? 'selected' : '' }}>Day</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <p class="text-muted">Leave empty to keep current password.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning text-white">Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const role = document.getElementById('role');
        const studentFields = document.getElementById('student_fields');
        
        function toggleFields() {
            if (role.value === 'student') {
                studentFields.style.display = 'flex';
            } else {
                studentFields.style.display = 'none';
            }
        }
        
        role.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
