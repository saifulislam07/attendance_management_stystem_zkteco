@extends('layouts.admin')

@section('title', 'Add New Student')
@section('page_title', 'Create Student')

@section('content')
<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @include('students._form', [
        'student' => null,
        'method' => 'POST',
        'buttonText' => 'Create Student',
    ])
</form>
@endsection
