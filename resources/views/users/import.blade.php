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
                    <p>Required columns: <code>Name, Email, DeviceUserID, ClassName, SectionName, Shift, Phone, Password</code></p>
                    <ul>
                        <li><strong>DeviceUserID</strong> must be unique and match the biometric device ID.</li>
                        <li><strong>ClassName</strong> and <strong>SectionName</strong> must match existing records in the system.</li>
                        <li><strong>Shift</strong> options: <code>Morning</code>, <code>Day</code>.</li>
                    </ul>
                </div>

                <form action="{{ route('users.import.process') }}" method="POST" enctype="multipart/form-data">
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
                        <a href="{{ route('users.index') }}" class="btn btn-default">Back to Users</a>
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
