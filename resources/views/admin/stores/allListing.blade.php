@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Stores</h2>

                <!-- Search & Filter -->
                <form method="GET" action="{{ route('dashboard.admin.stores') }}" class="d-flex gap-2 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by email" value="{{ request('search') }}">

                    <select name="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Pending</option>
                        <option value="3" {{ request('status_filter') == '3' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <button type="submit" class="btn btn-success">Filter</button>

                    @if(request()->has('search') || request()->has('status_filter'))
                        <a href="{{ route('dashboard.admin.stores') }}" class="btn btn-secondary">Reset</a>
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
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            {{-- Logo --}}
                            <td>
                                @php
                                    $logo = $store->logo ? asset('assets/store_logo/' . $store->logo) : asset('assets/profile_image/default.png');
                                @endphp
                                <a href="{{ $logo }}" target="_blank">
                                    <img src="{{ $logo }}" alt="Logo" width="40" height="40" class="rounded-circle">
                                </a>
                            </td>
                            <td>{{ $store->name }}</td>
                            <td>{{ $store->email }}</td>
                            <td>{{ $store->mobile }}</td>
                            <td>
                                <span class="badge 
                                    @if($store->status_id == 1) bg-success
                                    @elseif($store->status_id == 2) bg-warning
                                    @else bg-danger @endif">
                                    @if($store->status_id == 1) Active
                                    @elseif($store->status_id == 2) Pending
                                    @else Rejected @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- View Details --}}
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#storeDetailsModal{{ $store->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    {{-- Approve --}}
                                    @if($store->status_id == 2 || $store->status_id == 3)
                                        <form method="POST" action="{{ route('dashboard.admin.stores.approve', $store->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                    @endif

                                    {{-- Reject --}}
                                    @if($store->status_id == 1 || $store->status_id == 2)
                                        <form method="POST" action="{{ route('dashboard.admin.stores.reject', $store->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No stores found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($stores->lastPage() > 1)
                <nav class="pt-3">
                    {{ $stores->links() }}
                </nav>
            @endif
        </div>

        {{-- Store Details Modals --}}
        @foreach($stores as $store)
            <div class="modal fade" id="storeDetailsModal{{ $store->id }}" tabindex="-1" aria-labelledby="storeDetailsLabel{{ $store->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="storeDetailsLabel{{ $store->id }}">Store Details: {{ $store->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                {{-- Logo --}}
                                <div class="col-md-4 text-center mb-3">
                                    <a href="{{ $logo }}" target="_blank">
                                        <img src="{{ $logo }}" class="img-fluid rounded" alt="Logo">
                                    </a>
                                </div>

                                {{-- Details --}}
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr><th>Name</th><td>{{ $store->name }}</td></tr>
                                        <tr><th>Email</th><td>{{ $store->email }}</td></tr>
                                        <tr><th>Mobile</th><td>{{ $store->mobile }}</td></tr>
                                        <tr><th>Country</th><td>{{ $store->country }}</td></tr>
                                        <tr><th>City</th><td>{{ $store->city }}</td></tr>
                                        <tr><th>Address</th><td>{{ $store->address }}</td></tr>
                                        <tr><th>Type</th>
                                            <td>
                                                @if($store->type == 1) Retail
                                                @elseif($store->type == 2) Online
                                                @else Wholesale
                                                @endif
                                            </td>
                                        </tr>
                                        <tr><th>Delivery by Seller</th><td>{{ $store->delivery_by_seller ? 'Yes' : 'No' }}</td></tr>
                                        <tr><th>Self Pickup</th><td>{{ $store->self_pickup ? 'Yes' : 'No' }}</td></tr>
                                        <tr><th>Working Hours</th><td>{{ $store->working_hours }}</td></tr>
                                        <tr><th>Description</th><td>{{ $store->description }}</td></tr>
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
