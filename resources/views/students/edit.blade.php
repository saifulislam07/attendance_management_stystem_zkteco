@extends('layouts.admin')

@section('title', 'Edit Student')
@section('page_title', 'Update Student')

@section('content')
<form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
    @include('students._form', [
        'method' => 'PUT',
        'buttonText' => 'Update Student',
    ])
</form>
@endsection
