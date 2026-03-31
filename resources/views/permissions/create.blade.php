@extends('layouts.admin')

@section('title', 'Add New Permission')
@section('page_title', 'Create Permission')

@section('content')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="card card-info">
                <div class="card-header"><h3 class="card-title">New System Permission</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Permission Name (Slug-style)</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. view-billing" required>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-info">Create Permission</button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
