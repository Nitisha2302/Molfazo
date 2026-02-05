@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="ai-training-data-wrapper d-flex justify-content-between">
    <h2>Orders</h2>

    <form method="GET" class="d-flex gap-2">
        <input type="number" name="order_id"
               class="form-control" style="width:160px"
               placeholder="Order ID"
               value="{{ request('order_id') }}">

        <select name="status_id" class="form-control" style="width:180px">
            <option value="">All Status</option>
            <option value="1">New</option>
            <option value="2">Accepted</option>
            <option value="3">Completed</option>
            <option value="4">Cancelled</option>
        </select>

        <button class="btn btn-dark">Filter</button>
    </form>
</div>

<div class="project-ongoing-box mt-3">
<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Order #</th>
    <th>Customer</th>
    <th>Store</th>
    <th>Total</th>
    <th>Status</th>
    <th>Payment</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($orders as $order)
<tr>
    <td>#{{ $order->id }}</td>
    <td>{{ $order->user?->name ?? $order->user?->mobile }}</td>
    <td>{{ $order->store?->name }}</td>
    <td>₹{{ $order->total_amount }}</td>

    <td>
        <span class="badge
            @if($order->status_id == 1) bg-warning
            @elseif($order->status_id == 2) bg-info
            @elseif($order->status_id == 3) bg-success
            @else bg-danger @endif">
            {{ ['','New','Accepted','Completed','Cancelled'][$order->status_id] }}
        </span>
    </td>

    <td>{{ strtoupper($order->payment_type) }}</td>
    <td>{{ $order->created_at->format('d M Y') }}</td>

    <td>
        <button class="btn btn-info btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#orderModal{{ $order->id }}">
            <i class="fa fa-eye"></i>
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center">No orders found</td>
</tr>
@endforelse
</tbody>
</table>

{{ $orders->links() }}
</div>

{{-- ORDER DETAILS MODAL --}}
@foreach($orders as $order)
<div class="modal fade" id="orderModal{{ $order->id }}">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
    <h5>Order #{{ $order->id }} Details</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<h6>Customer</h6>
<p>
    {{ $order->user?->name }} <br>
    {{ $order->delivery_address }}
</p>

<h6>Items</h6>
<table class="table table-bordered">
<tr>
    <th>Product</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Total</th>
</tr>

@foreach($order->items as $item)
<tr>
    <td>{{ $item->product?->name }}</td>
    <td>{{ $item->quantity }}</td>
    <td>₹{{ $item->price }}</td>
    <td>₹{{ $item->price * $item->quantity }}</td>
</tr>
@endforeach
</table>

<h6 class="text-end">
    Grand Total: ₹{{ $order->total_amount }}
</h6>

</div>

</div>
</div>
</div>
@endforeach

</section>
</div>
@endsection
