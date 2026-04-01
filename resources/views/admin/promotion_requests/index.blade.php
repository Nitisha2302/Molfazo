@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Promotion Requests</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
</div>

<div class="project-ongoing-box">

<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>ID</th>
    <th>Vendor</th>
    <th>Product</th>
    <th>Package</th>
    <th>Reviews</th>
    <th>Price</th>
    <th>Screenshot</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($requests as $req)
<tr>
    <td>{{ $req->id }}</td>

    <td>{{ $req->vendor->name ?? '-' }}</td>

    <td>{{ $req->product->name ?? '-' }}</td>

    <td>{{ $req->package->title ?? '-' }}</td>

    <td>{{ $req->package->review_count ?? '-' }}</td>

    <td>{{ $req->package->price ?? '-' }}</td>

    <td>
        @if($req->payment_screenshot)
        <a href="{{ asset('assets/payment_screenshots/'.$req->payment_screenshot) }}" target="_blank">
            <img src="{{ asset('assets/payment_screenshots/'.$req->payment_screenshot) }}"
                 style="width:50px;height:50px;">
        </a>
        @endif
    </td>

    <td>
        <span class="badge 
            {{ $req->status == 'approved' ? 'bg-success' : ($req->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
            {{ ucfirst($req->status) }}
        </span>
    </td>

    <td>
        <div class="d-flex gap-2">

            @if($req->status == 'pending')
                
                <a href="{{ route('dashboard.admin.promotion.requests.approve',$req->id) }}"
                class="btn btn-success btn-sm">
                Approve
                </a>

                <a href="{{ route('dashboard.admin.promotion.requests.reject',$req->id) }}"
                class="btn btn-danger btn-sm">
                Reject
                </a>

            @elseif($req->status == 'approved')

                <a href="{{ route('dashboard.admin.promotion.requests.reject',$req->id) }}"
                class="btn btn-danger btn-sm">
                Reject
                </a>

            @elseif($req->status == 'rejected')

                <a href="{{ route('dashboard.admin.promotion.requests.approve',$req->id) }}"
                class="btn btn-success btn-sm">
                Approve
                </a>

            @endif

        </div>
    </td>

</tr>
@empty
<tr>
    <td colspan="9" class="text-center">No requests found.</td>
</tr>
@endforelse
</tbody>
</table>

{{ $requests->links() }}

</div>

</section>
</div>
@endsection