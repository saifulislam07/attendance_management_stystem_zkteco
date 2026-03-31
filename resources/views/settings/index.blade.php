@extends('layouts.admin')

@section('title', 'System Settings')
@section('page_title', 'Application Branding & Settings')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">General Configuration</h3>
            </div>
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="{{ \App\Models\Setting::get('site_name', config('app.name')) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Current Logo</label>
                                <div class="mb-2">
                                    <img src="{{ \App\Models\Setting::get('site_logo', asset('logo/logo.png')) }}" alt="Logo" style="max-height: 80px; background: #eee; padding: 10px;">
                                </div>
                                <label>Upload New Logo</label>
                                <input type="file" name="site_logo" class="form-control-file">
                                <small class="text-muted">Recommended: PNG with transparent background. Max 2MB.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Current Favicon</label>
                                <div class="mb-2">
                                    <img src="{{ \App\Models\Setting::get('site_favicon', asset('logo/logo.png')) }}" alt="Favicon" style="height: 32px;">
                                </div>
                                <label>Upload New Favicon</label>
                                <input type="file" name="site_favicon" class="form-control-file">
                                <small class="text-muted">Recommended: 32x32px PNG or ICO.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Footer Copyright Text</label>
                        <textarea name="footer_text" class="form-control" rows="2">{{ \App\Models\Setting::get('footer_text', 'Copyright ' . date('Y') . ' School Attendance Pro. All rights reserved.') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ \App\Models\Setting::get('contact_phone') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ \App\Models\Setting::get('contact_email') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Instructions</h3>
            </div>
            <div class="card-body">
                <p>These settings affect the global appearance of your School Attendance System.</p>
                <ul>
                    <li><strong>Site Name</strong>: Changes the title in the browser tab and sidebar.</li>
                    <li><strong>Logo</strong>: Updates the sidebar brand image.</li>
                    <li><strong>Favicon</strong>: Updates the small icon in the browser tab.</li>
                    <li><strong>Cache</strong>: Settings are cached for performance. Saving here will automatically clear the cache.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
