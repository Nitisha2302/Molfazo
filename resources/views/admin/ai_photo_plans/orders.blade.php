@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>AI Photo Purchases</h2>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card p-3">
                <small class="text-muted">Total Revenue</small>
                <h4 class="mb-0">{{ number_format($stats['total_revenue'], 2) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <small class="text-muted">Paid Orders</small>
                <h4 class="mb-0">{{ $stats['paid_count'] }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <small class="text-muted">Pending</small>
                <h4 class="mb-0">{{ $stats['pending_count'] }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <small class="text-muted">Failed</small>
                <h4 class="mb-0">{{ $stats['failed_count'] }}</h4>
            </div>
        </div>
    </div>

    <form method="GET"
          action="{{ route('dashboard.admin.ai-photo-orders.index') }}"
          class="d-flex gap-2 mb-3">

        <input type="text" name="search" class="form-control" style="width:250px"
               placeholder="Seller name / email / intent id"
               value="{{ request('search') }}">

        <select name="status" class="form-control" style="width:160px">
            <option value="">All statuses</option>
            @foreach(['pending','paid','failed','refunded','canceled'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                    {{ ucfirst($st) }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-dark">Filter</button>

        <a href="{{ route('dashboard.admin.ai-photo-orders.index') }}"
           class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="project-ongoing-box">
<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>#</th>
    <th>Seller</th>
    <th>Plan</th>
    <th>Credits</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Credits Given</th>
    <th>Stripe Intent</th>
    <th>Date</th>
</tr>
</thead>

<tbody>
@forelse($orders as $order)
<tr>
    <td>{{ $order->id }}</td>
    <td>
        {{ $order->vendor->name ?? 'Deleted user' }}<br>
        <small class="text-muted">{{ $order->vendor->email ?? $order->vendor->mobile ?? '' }}</small>
    </td>
    <td>{{ $order->plan_name }}</td>
    <td>{{ $order->credits }}</td>
    <td>{{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}</td>
    <td>
        @php
            $map = [
                'paid'     => 'success',
                'pending'  => 'warning',
                'failed'   => 'danger',
                'refunded' => 'dark',
                'canceled' => 'secondary',
            ];
        @endphp
        <span class="badge bg-{{ $map[$order->status] ?? 'secondary' }}">
            {{ ucfirst($order->status) }}
        </span>
    </td>
    <td>
        @if($order->credits_granted)
            <span class="badge bg-success">Yes</span>
        @else
            <span class="badge bg-secondary">No</span>
        @endif
    </td>
    <td><small>{{ $order->stripe_payment_intent_id ?? '—' }}</small></td>
    <td><small>{{ $order->created_at->format('d M Y H:i') }}</small></td>
</tr>
@empty
<tr><td colspan="9" class="text-center">No purchases found.</td></tr>
@endforelse
</tbody>
</table>

{{ $orders->links() }}
</div>

</section>
</div>
@endsection
