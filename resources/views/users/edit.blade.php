@extends('layouts.admin')

@php
    $isStudentEdit = $user->role === 'student';
@endphp

@section('title', $isStudentEdit ? 'Edit Student' : 'Edit User')
@section('page_title', $isStudentEdit ? 'Update Student' : 'Update User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">{{ $isStudentEdit ? 'Student Information' : 'User Information' }}</h3>
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
                                <label for="email">Email @unless($isStudentEdit)<span class="text-danger">*</span>@endunless</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ str_ends_with($user->email, '@no-login.local') ? '' : $user->email }}" {{ $isStudentEdit ? '' : 'required' }}>
                                @if($isStudentEdit)
                                    <small class="text-muted">Optional. Students will not login.</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if($isStudentEdit)
                            <input type="hidden" name="role" id="role" value="student">
                        @else
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="teacher" {{ $user->role == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="office_boy" {{ $user->role == 'office_boy' ? 'selected' : '' }}>Office Boy</option>
                                    <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>General Staff</option>
                                </select>
                            </div>
                        </div>
                        @endif
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
                        <div class="col-12">
                            <h5 class="mt-2 mb-3 text-primary">Academic Information</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="admission_no">Admission No</label>
                                <input type="text" name="admission_no" id="admission_no" class="form-control" value="{{ old('admission_no', $user->admission_no) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="roll_no">Roll No <span class="text-danger">*</span></label>
                                <input type="text" name="roll_no" id="roll_no" class="form-control" value="{{ old('roll_no', $user->roll_no) }}">
                                <small class="text-muted">Unique within class and section.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="shift">Shift</label>
                                <select name="shift" id="shift" class="form-control">
                                    <option value="Morning" {{ $user->shift == 'Morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="Day" {{ $user->shift == 'Day' ? 'selected' : '' }}>Day</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="blood_group">Blood Group</label>
                                <input type="text" name="blood_group" id="blood_group" class="form-control" value="{{ old('blood_group', $user->blood_group) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mt-2 mb-3 text-primary">Parent / Guardian Information</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_name">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" id="guardian_name" class="form-control" value="{{ old('guardian_name', $user->guardian_name) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_relation">Relation</label>
                                <input type="text" name="guardian_relation" id="guardian_relation" class="form-control" value="{{ old('guardian_relation', $user->guardian_relation) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_phone">Guardian Phone <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_phone" id="guardian_phone" class="form-control" value="{{ old('guardian_phone', $user->guardian_phone) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_email">Guardian Email</label>
                                <input type="email" name="guardian_email" id="guardian_email" class="form-control" value="{{ old('guardian_email', $user->guardian_email) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div id="password_fields">
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
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning text-white">{{ $isStudentEdit ? 'Update Student' : 'Update User' }}</button>
                        <a href="{{ $isStudentEdit ? route('students.index') : route('users.index') }}" class="btn btn-default">Cancel</a>
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
        const passwordFields = document.getElementById('password_fields');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        function toggleFields() {
            if (role.value === 'student') {
                studentFields.style.display = 'flex';
                passwordFields.style.display = 'none';
                password.value = '';
                passwordConfirmation.value = '';
            } else {
                studentFields.style.display = 'none';
                passwordFields.style.display = 'block';
            }
        }
        
        role.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
