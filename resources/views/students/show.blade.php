@extends('layouts.admin')

@section('title', 'Student Profile')
@section('page_title', 'Student Profile')

@section('content')
<div class="row mb-3 no-print">
    <div class="col-md-12 text-right">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print mr-1"></i> Print Profile
        </button>
        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-info">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-default">Back</a>
    </div>
</div>

<div class="student-print-sheet">
    <div class="print-header">
        <div>
            <h2>{{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }}</h2>
            <p>Student Information Profile</p>
        </div>
        <div class="print-meta">
            <span>Printed: {{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <div class="profile-hero">
        <div class="student-photo">
            @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}">
            @else
                <div class="photo-placeholder">
                    <i class="fas fa-user-graduate"></i>
                </div>
            @endif
        </div>
        <div class="student-title">
            <h1>{{ $student->name }}</h1>
            <div class="student-badges">
                <span>Roll: {{ $student->roll_no ?? '--' }}</span>
                <span>Device ID: {{ $student->device_user_id }}</span>
                <span>{{ $student->schoolClass->name ?? '--' }} / {{ $student->section->name ?? '--' }}</span>
            </div>
        </div>
    </div>

    <div class="profile-grid print-table-layout">
        <section class="print-section">
            <h3>Academic Information</h3>
            <table>
                <tr><th>Admission No</th><td>{{ $student->admission_no ?? '--' }}</td></tr>
                <tr><th>Class</th><td>{{ $student->schoolClass->name ?? '--' }}</td></tr>
                <tr><th>Section</th><td>{{ $student->section->name ?? '--' }}</td></tr>
                <tr><th>Shift</th><td>{{ $student->shift ?? '--' }}</td></tr>
                <tr><th>Roll No</th><td>{{ $student->roll_no ?? '--' }}</td></tr>
            </table>
        </section>

        <section class="print-section">
            <h3>Personal Information</h3>
            <table>
                <tr><th>Gender</th><td>{{ $student->gender ?? '--' }}</td></tr>
                <tr><th>Date of Birth</th><td>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '--' }}</td></tr>
                <tr><th>Blood Group</th><td>{{ $student->blood_group ?? '--' }}</td></tr>
            </table>
        </section>

        <section class="print-section">
            <h3>Guardian Information</h3>
            <table>
                <tr><th>Name</th><td>{{ $student->guardian_name ?? '--' }}</td></tr>
                <tr><th>Relation</th><td>{{ $student->guardian_relation ?? '--' }}</td></tr>
                <tr><th>Phone</th><td>{{ $student->guardian_phone ?? '--' }}</td></tr>
                <tr><th>Email</th><td>{{ $student->guardian_email ?? '--' }}</td></tr>
            </table>
        </section>

        <section class="print-section">
            <h3>Address</h3>
            <p class="address-box">{{ $student->address ?? '--' }}</p>
        </section>
    </div>

    <div class="signature-row">
        <div>
            <span></span>
            <p>Guardian Signature</p>
        </div>
        <div>
            <span></span>
            <p>Authorized Signature</p>
        </div>
    </div>
</div>

<style>
    .student-print-sheet {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 28px;
        color: #111827;
    }
    .print-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 14px;
        margin-bottom: 22px;
    }
    .print-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }
    .print-header p,
    .print-meta {
        margin: 4px 0 0;
        color: #6c757d;
    }
    .profile-hero {
        display: flex;
        gap: 22px;
        align-items: center;
        margin-bottom: 24px;
    }
    .student-photo img,
    .photo-placeholder {
        width: 132px;
        height: 156px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        object-fit: cover;
        background: #f8f9fa;
    }
    .photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 56px;
    }
    .student-title h1 {
        margin: 0 0 12px;
        font-size: 30px;
        font-weight: 700;
    }
    .student-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .student-badges span {
        border: 1px solid #cfe2ff;
        background: #f0f6ff;
        color: #084298;
        border-radius: 4px;
        padding: 6px 10px;
        font-weight: 600;
    }
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .profile-grid section {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 16px;
        min-height: 100%;
    }
    .profile-grid h3 {
        font-size: 17px;
        font-weight: 700;
        margin: 0 0 12px;
        color: #0d6efd;
    }
    .print-section table {
        width: 100%;
        border-collapse: collapse;
    }
    .print-section th,
    .print-section td {
        padding: 7px 8px;
        border-bottom: 1px solid #f1f3f5;
        vertical-align: top;
    }
    .print-section th {
        width: 135px;
        color: #6c757d;
        font-weight: 600;
        text-align: left;
    }
    .print-section td {
        font-weight: 600;
    }
    .address-box {
        margin: 0;
        white-space: pre-wrap;
        line-height: 1.6;
    }
    .signature-row {
        display: flex;
        justify-content: space-between;
        gap: 80px;
        margin-top: 52px;
    }
    .signature-row div {
        flex: 1;
        text-align: center;
    }
    .signature-row span {
        display: block;
        border-top: 1px solid #111827;
        margin-bottom: 8px;
    }
    .signature-row p {
        margin: 0;
        font-weight: 600;
    }
    @media (max-width: 767.98px) {
        .student-print-sheet { padding: 16px; }
        .print-header,
        .profile-hero,
        .signature-row { display: block; }
        .student-photo { margin-bottom: 14px; }
        .profile-grid { grid-template-columns: 1fr; }
        .print-section th { width: 112px; }
        .signature-row div { margin-top: 42px; }
    }
    @media print {
        @page { size: A4; margin: 14mm; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html,
        body {
            background: #fff !important;
            font-size: 12px;
        }
        .main-header,
        .main-sidebar,
        .main-footer,
        .content-header,
        .no-print,
        .alert { display: none !important; }
        .content-wrapper,
        .content,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .student-print-sheet {
            border: 0;
            border-radius: 0;
            padding: 0;
            width: 100%;
        }
        .print-header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #0d6efd !important;
            margin-bottom: 14px;
        }
        .print-header > div {
            display: table-cell;
            vertical-align: top;
        }
        .print-meta {
            text-align: right;
        }
        .profile-hero {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .student-photo,
        .student-title {
            display: table-cell;
            vertical-align: middle;
        }
        .student-photo {
            width: 150px;
        }
        .student-photo img,
        .photo-placeholder {
            width: 118px;
            height: 138px;
        }
        .student-title h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .student-badges {
            display: block;
        }
        .student-badges span {
            display: inline-block;
            margin: 0 5px 6px 0;
            border: 1px solid #cfe2ff !important;
            background: #f0f6ff !important;
        }
        .profile-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .print-section {
            display: block;
            width: 100%;
            border: 1px solid #d9dee3 !important;
            border-radius: 0;
            padding: 10px;
            margin-bottom: 10px;
        }
        .print-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .print-section th,
        .print-section td {
            border: 1px solid #e5e7eb !important;
            padding: 6px 8px;
        }
        .print-section th {
            width: 34%;
            background: #f8f9fa !important;
        }
        .profile-grid section,
        .print-section {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 38px;
        }
        .signature-row div {
            display: table-cell;
            width: 50%;
            padding: 0 30px;
        }
    }
</style>
@endsection
