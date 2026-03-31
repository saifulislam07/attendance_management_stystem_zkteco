@extends('layouts.admin')

@section('title', 'Permission Management')
@section('page_title', 'System Permissions')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-info card-outline">
            <div class="card-header text-right">
                <a href="{{ route('permissions.create') }}" class="btn btn-info btn-sm"><i class="fas fa-plus"></i> Add New Permission</a>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($permissions as $permission)
                    <div class="col-md-3 mb-2">
                        <div class="info-box bg-light border shadow-none">
                            <span class="info-box-icon text-info"><i class="fas fa-key"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ str_replace('-', ' ', ucfirst($permission->name)) }}</span>
                            </div>
                            <div class="bg-light text-right p-1">
                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this permission?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
