@extends('layouts.admin')

@section('title', 'Add New Class')
@section('page_title', 'Add New Class')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Class Details</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group text-dark">
                        <label for="name">Class Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter class name (e.g., Nursery, Class 1)" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Create Class</button>
                    <a href="{{ route('classes.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
