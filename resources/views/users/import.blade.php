@extends('layouts.admin')

@section('title', 'Import Students')
@section('page_title', 'Bulk Student Upload')

@section('content')
<style>
    .student-import-card {
        position: relative;
        overflow: hidden;
    }

    .import-overlay {
        align-items: center;
        backdrop-filter: blur(3px);
        background: rgba(255, 255, 255, 0.86);
        bottom: 0;
        display: none;
        justify-content: center;
        left: 0;
        padding: 24px;
        position: absolute;
        right: 0;
        top: 0;
        z-index: 20;
    }

    .student-import-card.is-importing .import-overlay {
        display: flex;
    }

    .import-status {
        max-width: 320px;
        text-align: center;
        width: 100%;
    }

    .import-copy-flow {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 18px;
    }

    .import-copy-node {
        align-items: center;
        background: #f4f8ff;
        border: 1px solid #cfe2ff;
        border-radius: 8px;
        color: #007bff;
        display: inline-flex;
        height: 48px;
        justify-content: center;
        width: 48px;
    }

    .import-copy-track {
        background: #d9e8ff;
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
        position: relative;
        width: 92px;
    }

    .import-copy-dot {
        animation: importCopy 1s ease-in-out infinite;
        background: #007bff;
        border-radius: 50%;
        height: 12px;
        left: 0;
        position: absolute;
        top: -3px;
        width: 12px;
    }

    .import-percent {
        color: #007bff;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 8px;
    }

    .import-progress {
        background: #d9e8ff;
        border-radius: 999px;
        height: 8px;
        margin-top: 16px;
        overflow: hidden;
    }

    .import-progress-bar {
        background: linear-gradient(90deg, #007bff, #17a2b8);
        border-radius: inherit;
        height: 100%;
        transition: width 0.25s ease;
        width: 0;
    }

    @keyframes importCopy {
        0% {
            opacity: 0;
            transform: translateX(0) scale(0.8);
        }

        20% {
            opacity: 1;
        }

        80% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            transform: translateX(80px) scale(1);
        }
    }
</style>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary student-import-card" id="student-import-card">
            <div class="card-header">
                <h3 class="card-title">Upload CSV File</h3>
                <div class="card-tools">
                    <a href="{{ route('students.import.demo') }}" class="btn btn-tool text-white">
                        <i class="fas fa-download"></i> Download Demo CSV
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> CSV Format Guide</h5>
                    <p>
                        Download the demo file, open it in Excel, replace the sample rows, then save it as CSV before upload.
                    </p>
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

                <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data" id="student-import-form">
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
                        <button type="submit" class="btn btn-primary" id="student-import-submit">
                            <i class="fas fa-file-import mr-1"></i> Start Import
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-default">Back to Students</a>
                    </div>
                </form>

                <div class="import-overlay" id="student-import-overlay" aria-live="polite" aria-hidden="true">
                    <div class="import-status">
                        <div class="import-copy-flow">
                            <div class="import-copy-node">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div class="import-copy-track">
                                <span class="import-copy-dot"></span>
                            </div>
                            <div class="import-copy-node">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>
                        <div class="import-percent" id="student-import-percent">0%</div>
                        <h5 class="mb-1">Importing students...</h5>
                        <p class="text-muted mb-0">Please wait while the CSV file is being processed.</p>
                        <div class="import-progress">
                            <div class="import-progress-bar" id="student-import-progress"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.querySelector('.custom-file-input');
        var importForm = document.getElementById('student-import-form');
        var importCard = document.getElementById('student-import-card');
        var importOverlay = document.getElementById('student-import-overlay');
        var submitButton = document.getElementById('student-import-submit');
        var progressBar = document.getElementById('student-import-progress');
        var progressPercent = document.getElementById('student-import-percent');
        var progressTimer = null;

        fileInput.addEventListener('change', function(e) {
            var fileName = fileInput.files.length ? fileInput.files[0].name : 'Browse Files...';
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });

        importForm.addEventListener('submit', function() {
            importCard.classList.add('is-importing');
            importOverlay.setAttribute('aria-hidden', 'false');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Importing...';

            var progress = 0;
            progressTimer = window.setInterval(function() {
                var step = progress < 70 ? 4 : 1;
                progress = Math.min(progress + step, 96);
                progressBar.style.width = progress + '%';
                progressPercent.innerText = progress + '%';

                if (progress >= 96) {
                    window.clearInterval(progressTimer);
                }
            }, 260);
        });
    });
</script>
@endsection
