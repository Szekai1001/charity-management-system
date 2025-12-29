@forelse($applications as $application)
<tr>
    <td>{{ $application->id }}</td>
    <td>{{ $application->user->name ?? 'Unknown User' }}</td>
    <td>{{ $application->user->email }}</td>
    <td>
        @if($application->status == 'approved')
        <span class="badge bg-success">Approved</span>
        @elseif($application->status == 'pending')
        <span class="badge bg-warning text-dark">Pending</span>
        @else
        <span class="badge bg-danger">rejected</span>
        @endif
    </td>
    <td>
        <select name="statuses[{{ $application->id }}]" class="form-select">
            <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </td>
    <td>
        <a href="#" data-bs-toggle="modal" data-bs-target="#details{{$application->id}}">View</a>
    </td>
</tr>

<div class="modal fade" id="details{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Teacher Application Details #{{ $application->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($application->application_type == 'Teacher' && $application->user->teacher)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Name</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->name }}</dd>

                            <dt class="col-sm-3">IC Number</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->ic }}</dd>

                            <dt class="col-sm-3">Gender</dt>
                            <dd class="col-sm-9">{{ ucfirst($application->user->teacher->gender) }}</dd>

                            <dt class="col-sm-3">Birth Date</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->birth_date }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->phone_number }}</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">
                                {{ collect([
                        $application->user->teacher->street ?? 'N/A',
                        $application->user->teacher->area,
                        $application->user->teacher->city,
                        $application->user->teacher->state,
                        $application->user->teacher->zip
                    ])->filter()->implode(', ') }}
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold">Education Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Education Level</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->education_level }}</dd>

                            <dt class="col-sm-3">Field of Expertise</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->field_of_expertise }}</dd>

                            <dt class="col-sm-3">Experience</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->experience_years }} years</dd>

                            <dt class="col-sm-3">Experience Details</dt>
                            <dd class="col-sm-9">{{ $application->user->teacher->experience_details }}</dd>
                        </dl>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h6 class="fw-bold">Documents</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            @foreach($application->documents as $doc)
                            <dt class="col-sm-4">{{ $doc->type }}</dt>
                            <dd class="col-sm-8">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-decoration-none">View Document</a></li>
                            </dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<tr>
    <td colspan="9" class="text-center py-4">
        <div class="d-flex flex-column align-items-center text-muted">
            <i class="bi bi-box-seam display-6 mb-2"></i>
            <span class="fw-semibold">No application for teachers yet</span>
            <small class="text-secondary">New appllication will appear here once received.</small>
        </div>
    </td>
</tr>
@endforelse