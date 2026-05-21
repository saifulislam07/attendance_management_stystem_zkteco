@csrf
@if($method !== 'POST')
    @method($method)
@endif

<div class="row">
    <div class="col-lg-9">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Student Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->name ?? '') }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="device_user_id">Device User ID <span class="text-danger">*</span></label>
                            <input type="text" name="device_user_id" id="device_user_id" class="form-control @error('device_user_id') is-invalid @enderror" value="{{ old('device_user_id', $student->device_user_id ?? '') }}" required>
                            @error('device_user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            <small class="text-muted">Must be unique and match ZKTeco ID.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="admission_no">Admission No</label>
                            <input type="text" name="admission_no" id="admission_no" class="form-control @error('admission_no') is-invalid @enderror" value="{{ old('admission_no', $student->admission_no ?? '') }}">
                            @error('admission_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="class_id">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="section_id">Section</label>
                            <select name="section_id" id="section_id" class="form-control @error('section_id') is-invalid @enderror">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id', $student->section_id ?? '') == $section->id ? 'selected' : '' }}>{{ $section->name }} ({{ $section->schoolClass->name }})</option>
                                @endforeach
                            </select>
                            @error('section_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="roll_no">Roll No <span class="text-danger">*</span></label>
                            <input type="text" name="roll_no" id="roll_no" class="form-control @error('roll_no') is-invalid @enderror" value="{{ old('roll_no', $student->roll_no ?? '') }}" required>
                            @error('roll_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="shift">Shift</label>
                            <select name="shift" id="shift" class="form-control">
                                <option value="">Select Shift</option>
                                <option value="Morning" {{ old('shift', $student->shift ?? '') == 'Morning' ? 'selected' : '' }}>Morning</option>
                                <option value="Day" {{ old('shift', $student->shift ?? '') == 'Day' ? 'selected' : '' }}>Day</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $student->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $student->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="blood_group">Blood Group</label>
                            <select name="blood_group" id="blood_group" class="form-control">
                                <option value="">Select Blood Group</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodGroup)
                                    <option value="{{ $bloodGroup }}" {{ old('blood_group', $student->blood_group ?? '') == $bloodGroup ? 'selected' : '' }}>{{ $bloodGroup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3 text-primary">Parent / Guardian Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="guardian_name">Guardian Name <span class="text-danger">*</span></label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" required>
                            @error('guardian_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="guardian_relation">Relation</label>
                            <input type="text" name="guardian_relation" id="guardian_relation" class="form-control" value="{{ old('guardian_relation', $student->guardian_relation ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="guardian_phone">Guardian Phone <span class="text-danger">*</span></label>
                            <input type="text" name="guardian_phone" id="guardian_phone" class="form-control @error('guardian_phone') is-invalid @enderror" value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}" required>
                            @error('guardian_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="guardian_email">Guardian Email</label>
                            <input type="email" name="guardian_email" id="guardian_email" class="form-control @error('guardian_email') is-invalid @enderror" value="{{ old('guardian_email', $student->guardian_email ?? '') }}">
                            @error('guardian_email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $student->address ?? '') }}</textarea>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ $buttonText }}
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Student Photo</h3>
            </div>
            <div class="card-body text-center">
                @if(!empty($student?->photo))
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo" class="img-fluid img-thumbnail mb-3" style="max-height: 220px;">
                @else
                    <div class="border rounded py-5 mb-3 text-muted">
                        <i class="fas fa-user-graduate fa-4x"></i>
                    </div>
                @endif
                <input type="file" name="photo" id="photo" class="form-control-file @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                @error('photo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                <small class="text-muted d-block mt-2">JPG, PNG, or WEBP. Max 2MB.</small>
            </div>
        </div>
    </div>
</div>
