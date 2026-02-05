@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

    <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
        <div class="heading-content-box">
            <h2>Orders</h2>

            {{-- FILTER --}}
            <form method="GET"
                action="{{ route('dashboard.admin.orders') }}"
                class="d-flex gap-2 mb-3">

                <input type="text" name="store_name"
                    class="form-control" style="width:220px"
                    placeholder="Search Store Name"
                    value="{{ request('store_name') }}">

                <select name="status_id" class="form-control" style="width:180px">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status_id') == 1 ? 'selected' : '' }}>New</option>
                    <option value="2" {{ request('status_id') == 2 ? 'selected' : '' }}>Accepted</option>
                    <option value="3" {{ request('status_id') == 3 ? 'selected' : '' }}>Completed</option>
                    <option value="4" {{ request('status_id') == 4 ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button class="btn btn-dark">Filter</button>

                @if(request()->hasAny(['store_name','status_id']))
                    <a href="{{ route('dashboard.admin.orders') }}"
                    class="btn btn-secondary">Reset</a>
                @endif
            </form>


            

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>

    <div class="project-ongoing-box">
        <table class="table table-striped table-bordered table-notification-list">
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

                    <td>
                        <strong>{{ $order->user?->name ?? 'N/A' }}</strong><br>
                        <small class="text-muted">
                            {{ $order->user?->mobile ?? '' }}
                        </small>
                    </td>

                    <td>{{ $order->store?->name ?? 'N/A' }}</td>

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

                    <td>{{ strtoupper($order->payment_type ?? '-') }}</td>

                    <td>{{ $order->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#orderDetailsModal{{ $order->id }}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No orders found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>

    {{-- MODALS --}}
    @foreach($orders as $order)
    <div class="modal fade" id="orderDetailsModal{{ $order->id }}">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Order Details: #{{ $order->id }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <tr>
                        <th>Customer</th>
                        <td>
                            {{ $order->user?->name ?? 'N/A' }} <br>
                            <small class="text-muted">{{ $order->user?->mobile ?? '' }}</small>
                        </td>
                    </tr>

                    <tr>
                        <th>Store</th>
                        <td>{{ $order->store?->name ?? 'N/A' }}</td>
                    </tr>

                    <tr>
                        <th>Delivery Address</th>
                        <td>{{ $order->delivery_address ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge
                                @if($order->status_id == 1) bg-warning
                                @elseif($order->status_id == 2) bg-info
                                @elseif($order->status_id == 3) bg-success
                                @else bg-danger @endif">
                                {{ ['','New','Accepted','Completed','Cancelled'][$order->status_id] }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Payment Type</th>
                        <td>{{ strtoupper($order->payment_type ?? '-') }}</td>
                    </tr>

                    <tr>
                        <th>Total Amount</th>
                        <td><strong>₹{{ $order->total_amount }}</strong></td>
                    </tr>

                    <tr>
                        <th>Date</th>
                        <td>{{ $order->created_at->format('d M Y h:i A') }}</td>
                    </tr>
                </table>

                <h6 class="mt-3">Order Items</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ $item->price }}</td>
                            <td>₹{{ $item->price * $item->quantity }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <h6 class="text-end">
                    Grand Total: ₹{{ $order->total_amount }}
                </h6>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
            </div>

        </div>
        </div>
    </div>
    @endforeach

</section>
</div>
@endsection
