@extends('layouts.admin')

@php
    $selectedRole = old('role', $selectedRole ?? request('role', 'student'));
    $isStudentCreate = $isStudentCreate ?? $selectedRole === 'student';
@endphp

@section('title', $isStudentCreate ? 'Add New Student' : 'Add New User')
@section('page_title', $isStudentCreate ? 'Create Student' : 'Create User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $isStudentCreate ? 'Student Information' : 'User Information' }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email @unless($isStudentCreate)<span class="text-danger">*</span>@endunless</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" {{ $isStudentCreate ? '' : 'required' }}>
                                @if($isStudentCreate)
                                    <small class="text-muted">Optional. Students will not login.</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if($isStudentCreate)
                            <input type="hidden" name="role" id="role" value="student">
                        @else
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="teacher" {{ $selectedRole == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="admin" {{ $selectedRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="operator" {{ $selectedRole == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="accountant" {{ $selectedRole == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="office_boy" {{ $selectedRole == 'office_boy' ? 'selected' : '' }}>Office Boy</option>
                                    <option value="staff" {{ $selectedRole == 'staff' ? 'selected' : '' }}>General Staff</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="device_user_id">Device User ID <span class="text-danger">*</span></label>
                                <input type="text" name="device_user_id" id="device_user_id" class="form-control" placeholder="101" value="{{ old('device_user_id') }}" required>
                                <small class="text-muted">Must match the ID on ZKTeco device.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="017xxxxxxxx" value="{{ old('phone') }}">
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
                                <input type="text" name="admission_no" id="admission_no" class="form-control" placeholder="ADM-001" value="{{ old('admission_no') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="class_id">Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="class_id" class="form-control">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
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
                                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }} ({{ $section->schoolClass->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="roll_no">Roll No <span class="text-danger">*</span></label>
                                <input type="text" name="roll_no" id="roll_no" class="form-control" placeholder="01" value="{{ old('roll_no') }}">
                                <small class="text-muted">Unique within class and section.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="shift">Shift</label>
                                <select name="shift" id="shift" class="form-control">
                                    <option value="Morning" {{ old('shift') == 'Morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="Day" {{ old('shift') == 'Day' ? 'selected' : '' }}>Day</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="blood_group">Blood Group</label>
                                <input type="text" name="blood_group" id="blood_group" class="form-control" placeholder="B+" value="{{ old('blood_group') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mt-2 mb-3 text-primary">Parent / Guardian Information</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_name">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" id="guardian_name" class="form-control" value="{{ old('guardian_name') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_relation">Relation</label>
                                <input type="text" name="guardian_relation" id="guardian_relation" class="form-control" placeholder="Father" value="{{ old('guardian_relation') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_phone">Guardian Phone <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_phone" id="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="guardian_email">Guardian Email</label>
                                <input type="email" name="guardian_email" id="guardian_email" class="form-control" value="{{ old('guardian_email') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="password_fields">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ $isStudentCreate ? 'Create Student' : 'Create User' }}</button>
                        <a href="{{ $isStudentCreate ? route('students.index') : route('users.index') }}" class="btn btn-default">Cancel</a>
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
                password.required = false;
                passwordConfirmation.required = false;
                password.value = '';
                passwordConfirmation.value = '';
            } else {
                studentFields.style.display = 'none';
                passwordFields.style.display = 'flex';
                password.required = true;
                passwordConfirmation.required = true;
            }
        }
        
        role.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
