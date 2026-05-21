@extends('layouts.admin')

@section('title', 'Import Students')
@section('page_title', 'Bulk Student Upload')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Upload CSV File</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> CSV Format Guide</h5>
                    <p>Required columns: <code>Name, DeviceUserID, AdmissionNo, RollNo, ClassName, SectionName, Shift, Gender, BloodGroup, GuardianName, GuardianRelation, GuardianPhone, GuardianEmail, Address</code></p>
                    <ul>
                        <li><strong>DeviceUserID</strong> must be unique and match the biometric device ID.</li>
                        <li><strong>Gender</strong> is required. Allowed: <code>Male</code>, <code>Female</code>, <code>Other</code>.</li>
                        <li><strong>BloodGroup</strong> options: <code>A+</code>, <code>A-</code>, <code>B+</code>, <code>B-</code>, <code>AB+</code>, <code>AB-</code>, <code>O+</code>, <code>O-</code>.</li>
                        <li><strong>RollNo</strong> should be unique within the student's class and section.</li>
                        <li><strong>GuardianPhone</strong> is recommended for emergency contact and SMS alerts.</li>
                        <li><strong>ClassName</strong> and <strong>SectionName</strong> must match existing records in the system.</li>
                        <li><strong>Shift</strong> options: <code>Morning</code>, <code>Day</code>.</li>
                        <li>Student photo should be uploaded from the student create/edit form.</li>
                    </ul>
                </div>

                <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Choose CSV File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="csv_file" class="custom-file-input" id="csv_file" accept=".csv" required>
                                <label class="custom-file-label" for="csv_file">Browse Files...</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Start Import</button>
                        <a href="{{ route('students.index') }}" class="btn btn-default">Back to Students</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = document.getElementById("csv_file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
</script>
@endsection
