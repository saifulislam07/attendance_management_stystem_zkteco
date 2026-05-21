@extends('layouts.admin')

@section('title', 'Edit Permission')
@section('page_title', 'Update Permission')

@section('content')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-info">
                <div class="card-header"><h3 class="card-title">Permission Information</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required>
                        @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-info">Update Permission</button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
