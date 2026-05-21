@extends('layouts.admin')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>
                <p class="text-muted text-center text-capitalize">{{ $user->roles->first()->name ?? $user->role }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <span class="float-right">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <span class="float-right">{{ $user->phone ?? '--' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Device ID</b> <span class="float-right">{{ $user->device_user_id ?? '--' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Class</b> <span class="float-right">{{ $user->schoolClass->name ?? '--' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Section</b> <span class="float-right">{{ $user->section->name ?? '--' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Account Information</h3>
            </div>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save mr-1"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="current_password">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-key mr-1"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
