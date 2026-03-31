@extends('layouts.admin')

@section('title', 'Add New Section')
@section('page_title', 'Create Section')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Section Information</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('sections.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Section Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="A" required>
                    </div>
                    <div class="form-group">
                        <label for="class_id">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Section</button>
                        <a href="{{ route('sections.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
