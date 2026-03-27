@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <h2>Product Details: {{ $product->name }}</h2>

        {{-- PRIMARY IMAGE --}}
        @php
            $primaryImage = $product->primaryImage 
                ? asset('assets/product_images/'.$product->primaryImage->image) 
                : asset('assets/no-image.png');
        @endphp
        <div class="mb-3">
            <h6>Primary Image</h6>
            <img src="{{ $primaryImage }}" 
                 style="width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">
        </div>

        {{-- ALL IMAGES --}}
        @if($product->images->count())
            <div class="mb-3">
                <h6>All Images</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->images as $img)
                        <img src="{{ asset('assets/product_images/'.$img->image) }}" 
                             style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PRODUCT DETAILS --}}
        <table class="table table-bordered">
            <tr><th>Name</th><td>{{ $product->name }}</td></tr>
            <tr><th>Store</th><td>{{ $product->store?->name }}</td></tr>
            <tr><th>Category</th><td>{{ $product->category?->name }}</td></tr>
            <tr><th>Subcategory</th><td>{{ $product->subCategory?->name ?? '-' }}</td></tr>
            <tr><th>Price</th><td>₹{{ $product->price }}</td></tr>
            <tr><th>Discount Price</th><td>{{ $product->discount_price ?? '-' }}</td></tr>
            <tr><th>Quantity</th>
                <td>
                    @if($product->available_quantity > 0)
                        <span class="badge bg-success">{{ $product->available_quantity }}</span>
                    @else
                        <span class="badge bg-danger">Out of stock</span>
                    @endif
                </td>
            </tr>
            <tr><th>Delivery Available</th><td>{{ $product->delivery_available ? 'Yes' : 'No' }}</td></tr>
            <tr><th>Description</th><td>{{ $product->description }}</td></tr>

            {{-- BANK DETAILS --}}
            @if($product->payment_mode == 'bank' && $product->store?->vendorBanks->count())
                <tr>
                    <th>Bank Details</th>
                    <td>
                        @foreach($product->store->vendorBanks as $bank)
                            <div>
                                <strong>{{ $bank->bank->name ?? '-' }}</strong>: {{ $bank->account_number ?? '-' }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endif

            {{-- ATTRIBUTES --}}
            @if(!empty($product->attributes_json))
                <tr>
                    <th>Attributes</th>
                    <td>
                        <ul class="mb-0">
                            @foreach($product->attributes_json as $key => $value)
                                <li>
                                    <strong>{{ ucfirst($key) }}:</strong>
                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endif
        </table>

        {{-- APPROVE / REJECT --}}
       {{-- APPROVE / REJECT --}}
        @if($product->approval_status == 'pending')
            <div class="mt-3">
                <!-- Reject with reason on one line -->
                <form method="POST" action="{{ route('dashboard.admin.products.reject', $product->id) }}" class="d-flex gap-2 mb-2">
                    @csrf
                    <textarea name="reject_reason" placeholder="Reason for rejection" class="form-control" rows="1" required></textarea>
                    <button class="btn btn-danger">Reject</button>
                </form>

                <!-- Approve button below -->
                <form method="POST" action="{{ route('dashboard.admin.products.approve', $product->id) }}">
                    @csrf
                    <button class="btn btn-success">Approve</button>
                </form>
            </div>
        @elseif($product->approval_status == 'approved')
            <div class="mt-3">
                <span class="badge bg-success">Approved</span>
            </div>
        @else
            <div class="mt-3">
                <span class="badge bg-danger">Rejected</span>
            </div>
        @endif

        <a href="{{ route('dashboard.admin.notifications.index') }}" class="btn btn-secondary mt-3">Back to Notifications</a>
    </section>
</div>
@endsection