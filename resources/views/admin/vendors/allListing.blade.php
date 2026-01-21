@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Vendors</h2>

                <!-- Search & Filter -->
                <form method="GET" action="{{ route('dashboard.admin.vendors') }}" class="d-flex gap-2 mb-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by email"
                           value="{{ request('search') }}">

                    <select name="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Pending</option>
                        <option value="3" {{ request('status_filter') == '3' ? 'selected' : '' }}>Rejected</option>
                        <option value="4" {{ request('status_filter') == '4' ? 'selected' : '' }}>Blocked</option>
                    </select>

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->has('search') || request()->has('status_filter'))
                        <a href="{{ route('dashboard.admin.vendors') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form>

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered table-notification-list">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            {{-- Profile Photo --}}
                            <td>
                                @php
                                    $profileImage = $vendor->profile_photo ? asset('assets/profile_image/' . $vendor->profile_photo) : asset('assets/profile_image/default.png');
                                @endphp
                                <a href="{{ $profileImage }}" target="_blank">
                                    <img src="{{ $profileImage }}" alt="Profile" class="rounded-circle" width="40" height="40">
                                </a>
                            </td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>{{ $vendor->mobile }}</td>
                            <td>
                                <span class="badge 
                                    @if($vendor->status_id == 1) bg-success
                                    @elseif($vendor->status_id == 2) bg-warning
                                    @elseif($vendor->status_id == 3) bg-danger
                                    @else bg-secondary @endif">
                                    @if($vendor->status_id == 1) Active
                                    @elseif($vendor->status_id == 2) Pending
                                    @elseif($vendor->status_id == 3) Rejected
                                    @else Blocked @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- View Details (Eye Icon) --}}
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#vendorDetailsModal{{ $vendor->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    {{-- Approve / Unblock --}}
                                    @if(in_array($vendor->status_id, [2,3]))
                                        <form method="POST" action="{{ route('dashboard.admin.vendors.approve', $vendor->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-dark btn-sm">Approve</button>
                                        </form>
                                    @elseif($vendor->status_id == 4)
                                        <form method="POST" action="{{ route('dashboard.admin.vendors.unblock', $vendor->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-dark btn-sm">Unblock</button>
                                        </form>
                                    @endif

                                    {{-- Reject --}}
                                    @if(in_array($vendor->status_id, [1,2]))
                                        <form method="POST" action="{{ route('dashboard.admin.vendors.reject', $vendor->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @endif

                                    {{-- Block --}}
                                    @if(in_array($vendor->status_id, [1,2,3]))
                                        <form method="POST" action="{{ route('dashboard.admin.vendors.block', $vendor->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">Block</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($vendors->lastPage() > 1)
                <nav class="pt-3">
                    {{ $vendors->links() }}
                </nav>
            @endif
        </div>

        {{-- Vendor Details Modals --}}
        @foreach($vendors as $vendor)
            <div class="modal fade" id="vendorDetailsModal{{ $vendor->id }}" tabindex="-1" aria-labelledby="vendorDetailsLabel{{ $vendor->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="vendorDetailsLabel{{ $vendor->id }}">Vendor Details: {{ $vendor->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                {{-- Profile Photo --}}
                                <div class="col-md-4 text-center mb-3">
                                    @php
                                        $profileImage = $vendor->profile_photo ? asset('assets/profile_image/' . $vendor->profile_photo) : asset('assets/profile_image/default.png');
                                    @endphp
                                    <a href="{{ $profileImage }}" target="_blank">
                                        <img src="{{ $profileImage }}" class="img-fluid rounded" alt="Profile Photo">
                                    </a>
                                </div>

                                {{-- Vendor Details Table --}}
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr><th>Name</th><td>{{ $vendor->name }}</td></tr>
                                        <tr><th>Email</th><td>{{ $vendor->email }}</td></tr>
                                        <tr><th>Mobile</th><td>{{ $vendor->mobile }}</td></tr>
                                        <tr><th>Country</th><td>{{ $vendor->country }}</td></tr>
                                        <tr><th>City</th><td>{{ $vendor->city }}</td></tr>
                                        <tr><th>Gov. ID Type</th><td>{{ $vendor->gov_id_type }}</td></tr>
                                        <tr><th>Gov. ID Number</th><td>{{ $vendor->gov_id_number }}</td></tr>
                                        <tr>
                                            <th>Government ID Documents</th>
                                            <td>
                                                @php
                                                    $govFiles = json_decode($vendor->government_id, true) ?? [];
                                                @endphp
                                                @foreach($govFiles as $file)
                                                    <a href="{{ asset('assets/gov_id_document/' . $file) }}" target="_blank">
                                                        <img src="{{ asset('assets/gov_id_document/' . $file) }}" alt="{{ $file }}" style="height:50px; margin:5px; border-radius:4px;">
                                                    </a>
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr><th>Terms Accepted</th><td>{{ $vendor->terms_accepted ? 'Yes' : 'No' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </section>
</div>
@endsection
