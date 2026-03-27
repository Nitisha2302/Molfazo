@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <h2>Store Details: {{ $store->name }}</h2>

        <table class="table table-bordered">
            <tr><th>Name</th><td>{{ $store->name }}</td></tr>
            <tr><th>Email</th><td>{{ $store->email }}</td></tr>
            <tr><th>Mobile</th><td>{{ $store->mobile }}</td></tr>
            <tr><th>Country</th><td>{{ $store->country }}</td></tr>
            <tr><th>City</th><td>{{ $store->city }}</td></tr>
            <tr><th>Address</th><td>{{ $store->address }}</td></tr>
            <tr><th>Status</th>
                <td>
                    @if($store->status_id == 1) Active
                    @elseif($store->status_id == 2) Pending
                    @else Rejected
                    @endif
                </td>
            </tr>
        </table>

        {{-- APPROVE / REJECT --}}
        @if($store->status_id == 2)
            <div class="d-flex gap-2 mt-2">
                <form method="POST" action="{{ route('dashboard.admin.stores.approve', $store->id) }}">
                    @csrf
                    <button class="btn btn-success">Approve</button>
                </form>

                <form method="POST" action="{{ route('dashboard.admin.stores.reject', $store->id) }}">
                    @csrf
                    <input type="text" name="reject_reason" placeholder="Reason for rejection" class="form-control mb-2" required>
                    <button class="btn btn-danger">Reject</button>
                </form>
            </div>
        @endif

        <a href="{{ route('dashboard.admin.notifications.index') }}" class="btn btn-secondary mt-3">Back to Notifications</a>
    </section>
</div>
@endsection