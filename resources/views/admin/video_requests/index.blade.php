@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Video Plan Requests</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
</div>

<div class="project-ongoing-box">
    

<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>Vendor</th>
    <th>Store</th>
    <th>Plan</th>
    <th>Duration</th>
    <th>Price</th>
    <th>Screenshot</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($requests as $req)
<tr>

    <!-- Vendor -->
    <td>{{ $req->vendor->name ?? '-' }}</td>

    <!-- Store -->
    <td>{{ $req->store->name ?? '-' }}</td>

    
    <!-- Plan -->
    <td>{{ $req->plan->name ?? '-' }}</td>

    <!-- Duration -->
    <td>{{ $req->plan->duration_days ?? '-' }} Days</td>

    <!-- Price -->
    <td>c. {{ $req->plan->price ?? '-' }}</td>

    <!-- Screenshot -->
    <td>
        @if($req->payment_screenshot)
            <a href="{{ asset('assets/payment_screenshots/'.$req->payment_screenshot) }}" target="_blank">
                <img src="{{ asset('assets/payment_screenshots/'.$req->payment_screenshot) }}"
                     style="width:50px;height:50px;border-radius:5px;">
            </a>
        @else
            -
        @endif
    </td>

    <!-- Status -->
    <td>
        <span class="badge 
            {{ $req->status == 'approved' ? 'bg-success' : ($req->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
            {{ ucfirst($req->status) }}
        </span>
    </td>

    <!-- Action -->
    <td>
        <div class="d-flex gap-2">

            @if($req->status == 'pending')
                
                <a href="{{ route('dashboard.admin.video.requests.approve',$req->id) }}"
                   class="btn btn-success btn-sm">
                   Approve
                </a>

                <a href="{{ route('dashboard.admin.video.requests.reject',$req->id) }}"
                   class="btn btn-danger btn-sm">
                   Reject
                </a>

            @elseif($req->status == 'approved')

                <a href="{{ route('dashboard.admin.video.requests.reject',$req->id) }}"
                   class="btn btn-danger btn-sm">
                   Reject
                </a>

            @elseif($req->status == 'rejected')

                <a href="{{ route('dashboard.admin.video.requests.approve',$req->id) }}"
                   class="btn btn-success btn-sm">
                   Approve
                </a>

            @endif

        </div>
    </td>

</tr>
@empty
<tr>
    <td colspan="8" class="text-center">No requests found.</td>
</tr>
@endforelse
</tbody>
</table>

<!-- Pagination -->
<div class="mt-3">
    {{ $requests->links() }}
</div>

</div>

</section>
</div>
@endsection