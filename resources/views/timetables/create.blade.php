@extends('layouts.admin')

@section('title', 'Create Timetable')
@section('page_title', 'Add New Timetable')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Timetable Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('timetables.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="class_group">
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="day">Day <span class="text-danger">*</span></label>
                                <select name="day" id="day" class="form-control" required>
                                    <option value="All Days">All Days</option>
                                    @foreach($days as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="grace_time">Grace Time (Minutes)</label>
                                <input type="number" name="grace_time" id="grace_time" class="form-control" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="in_time">In Time <span class="text-danger">*</span></label>
                                <input type="time" name="in_time" id="in_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="late_time">Late After <span class="text-danger">*</span></label>
                                <input type="time" name="late_time" id="late_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="out_time">Out Time <span class="text-danger">*</span></label>
                                <input type="time" name="out_time" id="out_time" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="half_day_time">Half Day After</label>
                                <input type="time" name="half_day_time" id="half_day_time" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6" id="overtime_group" style="display: none;">
                            <div class="form-group">
                                <label for="overtime_start">Overtime Starts After</label>
                                <input type="time" name="overtime_start" id="overtime_start" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary btn-lg">Create Timetable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const classGroup = document.getElementById('class_group');
        const overtimeGroup = document.getElementById('overtime_group');
        
        function updateFields() {
            if (roleSelect.value === 'teacher') {
                classGroup.style.display = 'none';
                overtimeGroup.style.display = 'block';
            } else {
                classGroup.style.display = 'block';
                overtimeGroup.style.display = 'none';
            }
        }
        
        roleSelect.addEventListener('change', updateFields);
        updateFields();
    });
</script>
@endsection
