@extends('layouts.admin')

@section('title', 'Manage Permissions')
@section('page_title', 'Permissions for: ' . ucfirst($role->name))

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Assign Permissions to Role</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label>Role Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                    </div>

                    <h5 class="mb-3">Toggle Permissions</h5>
                    <div class="row">
                        @foreach($permissions as $permission)
                        <div class="col-md-3 mb-3">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="permissions[]" id="perm_{{ $permission->id }}" value="{{ $permission->name }}" {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                <label for="perm_{{ $permission->id }}" class="custom-control-label">
                                    {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning text-white">Save Role Permissions</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
