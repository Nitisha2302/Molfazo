@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Customers</h2>

                <!-- Search & Filter (MATCHED DESIGN) -->
                <form method="GET" action="{{ route('dashboard.admin.customers') }}" class="d-flex gap-2 mb-3">
                    
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by email"
                           value="{{ request('search') }}">

                    <!-- <select name="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="4" {{ request('status_filter') == '4' ? 'selected' : '' }}>Blocked</option>
                    </select> -->

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->has('search') || request()->has('status_filter'))
                        <a href="{{ route('dashboard.admin.customers') }}" class="btn btn-secondary">Reset</a>
                    @endif

                </form>

                @if (session('success'))
                    <div class="alert alert-success">
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
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            {{-- Profile --}}
                            <td>
                                @php
                                    $profile = $customer->profile_photo
                                        ? asset('assets/profile_image/' . $customer->profile_photo)
                                        : asset('assets/profile_image/default.png');
                                @endphp

                                <a href="{{ $profile }}" target="_blank">
                                    <img src="{{ $profile }}" class="rounded-circle" width="40" height="40">
                                </a>
                            </td>

                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->mobile }}</td>

                            {{-- Status --}}
                            <!-- <td>
                                <span class="badge 
                                    {{ $customer->status_id == 1 ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $customer->status_id == 1 ? 'Active' : 'Blocked' }}
                                </span>
                            </td> -->

                            {{-- Action --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    {{-- View --}}
                                    <button type="button" class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#customerDetailsModal{{ $customer->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    {{-- Block / Unblock --}}
                                    <!-- @if($customer->status_id == 1)
                                        <form method="POST" action="{{ route('dashboard.admin.customers.block', $customer->id) }}">
                                            @csrf
                                            <button class="btn btn-secondary btn-sm">Block</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('dashboard.admin.customers.unblock', $customer->id) }}">
                                            @csrf
                                            <button class="btn btn-dark btn-sm">Unblock</button>
                                        </form>
                                    @endif -->

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($customers->lastPage() > 1)
                <nav class="pt-3">
                    {{ $customers->links() }}
                </nav>
            @endif
        </div>

        {{-- Customer Details Modal (Same Style as Vendor) --}}
        @foreach($customers as $customer)
        <div class="modal fade" id="customerDetailsModal{{ $customer->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Customer Details: {{ $customer->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-4 text-center mb-3">
                                @php
                                    $profile = $customer->profile_photo
                                        ? asset('assets/profile_photo/' . $customer->profile_photo)
                                        : asset('assets/profile_photo/default.png');
                                @endphp

                                <a href="{{ $profile }}" target="_blank">
                                    <img src="{{ $profile }}" class="img-fluid rounded">
                                </a>
                            </div>

                            <div class="col-md-8">
                                <table class="table table-bordered">
                                    <tr><th>Name</th><td>{{ $customer->name }}</td></tr>
                                    <tr><th>Mobile</th><td>{{ $customer->mobile }}</td></tr>
                                    <tr><th>Alternate Mobile</th><td>{{ $customer->alt_mobile }}</td></tr>
                                    <tr><th>Country</th><td>{{ $customer->country ?? '-' }}</td></tr>
                                    <tr><th>City</th><td>{{ $customer->city ?? '-' }}</td></tr>
                                    <!-- <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge 
                                                {{ $customer->status_id == 1 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $customer->status_id == 1 ? 'Active' : 'Blocked' }}
                                            </span>
                                        </td>
                                    </tr> -->
                                    <!-- <tr>
                                        <th>Joined At</th>
                                        <td>{{ $customer->created_at->format('d M Y, h:i A') }}</td>
                                    </tr> -->
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
        @endforeach

    </section>
</div>
@endsection