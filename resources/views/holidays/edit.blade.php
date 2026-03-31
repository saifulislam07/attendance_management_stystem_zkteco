@extends('layouts.admin')

@section('title', 'Edit Holiday')
@section('page_title', 'Update Holiday')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Holiday Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="date">Holiday Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ $holiday->date->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Holiday Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $holiday->title }}" required>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Holiday</button>
                        <a href="{{ route('holidays.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
