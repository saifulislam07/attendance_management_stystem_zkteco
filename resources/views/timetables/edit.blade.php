@extends('layouts.admin')

@section('title', 'Edit Timetable')
@section('page_title', 'Update Timetable')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Timetable Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('timetables.update', $timetable->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control" required disabled>
                                    <option value="student" {{ $timetable->role == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ $timetable->role == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                </select>
                                <input type="hidden" name="role" value="{{ $timetable->role }}">
                            </div>
                        </div>
                        <div class="col-md-6" id="class_group">
                            <div class="form-group">
                                <label for="class_id">Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="class_id" class="form-control" disabled>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $timetable->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="class_id" value="{{ $timetable->class_id }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="day">Day <span class="text-danger">*</span></label>
                                <select name="day" id="day" class="form-control" required>
                                    @foreach($days as $day)
                                        <option value="{{ $day }}" {{ $timetable->day == $day ? 'selected' : '' }}>{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="grace_time">Grace Time (Minutes)</label>
                                <input type="number" name="grace_time" id="grace_time" class="form-control" value="{{ $timetable->grace_time ?? 0 }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="in_time">In Time <span class="text-danger">*</span></label>
                                <input type="time" name="in_time" id="in_time" class="form-control" value="{{ $timetable->in_time }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="late_time">Late After <span class="text-danger">*</span></label>
                                <input type="time" name="late_time" id="late_time" class="form-control" value="{{ $timetable->late_time }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="out_time">Out Time <span class="text-danger">*</span></label>
                                <input type="time" name="out_time" id="out_time" class="form-control" value="{{ $timetable->out_time }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="half_day_time">Half Day After</label>
                                <input type="time" name="half_day_time" id="half_day_time" class="form-control" value="{{ $timetable->half_day_time }}">
                            </div>
                        </div>
                        <div class="col-md-6" id="overtime_group">
                            <div class="form-group">
                                <label for="overtime_start">Overtime Starts After</label>
                                <input type="time" name="overtime_start" id="overtime_start" class="form-control" value="{{ $timetable->overtime_start }}">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary btn-lg">Update Timetable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const role = '{{ $timetable->role }}';
        const classGroup = document.getElementById('class_group');
        const overtimeGroup = document.getElementById('overtime_group');
        
        if (role === 'teacher') {
            classGroup.style.display = 'none';
            overtimeGroup.style.display = 'block';
        } else {
            classGroup.style.display = 'block';
            overtimeGroup.style.display = 'none';
        }
    });
</script>
@endsection
