@extends('layouts.admin')

@section('title', 'Add Holiday')
@section('page_title', 'Create Holiday')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Holiday Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('holidays.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="date">Holiday Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Holiday Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Independence Day" value="{{ old('title') }}" required>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Holiday</button>
                        <a href="{{ route('holidays.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
