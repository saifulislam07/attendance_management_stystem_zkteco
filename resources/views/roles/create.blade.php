@extends('layouts.admin')

@section('title', 'Add New Role')
@section('page_title', 'Create Role')

@section('content')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Role Title</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Role Name (Unique)</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. librarian" required>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
