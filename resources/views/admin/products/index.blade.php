@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

    <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
        <div class="heading-content-box">
            <h2>Products</h2>

            {{-- SEARCH --}}
            <form method="GET"
                  action="{{ route('dashboard.admin.products') }}"
                  class="d-flex gap-2 mb-3">

                <input type="text" name="search"
                       class="form-control" style="width:220px"
                       placeholder="Search by product name"
                       value="{{ request('search') }}">

                <input type="number" name="min_price"
                       class="form-control" style="width:140px"
                       placeholder="Min price"
                       value="{{ request('min_price') }}">

                <input type="number" name="max_price"
                       class="form-control" style="width:140px"
                       placeholder="Max price"
                       value="{{ request('max_price') }}">

                <button class="btn btn-dark">Filter</button>

                @if(request()->hasAny(['search','min_price','max_price']))
                    <a href="{{ route('dashboard.admin.products') }}"
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
                    <th>Product</th>
                    <th>Store</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($products as $product)

                @php
                    $image = $product->primaryImage
                        ? asset('assets/product_images/'.$product->primaryImage->image)
                        : asset('assets/no-image.png');
                @endphp

                <tr>
                    {{-- PRODUCT --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ $image }}" target="_blank">
                                <img src="{{ $image }}"
                                     width="40" height="40"
                                     style="object-fit:cover;border-radius:6px;">
                            </a>

                            <div>
                                <strong>{{ $product->name }}</strong><br>
                                <small class="text-muted">
                                    Qty: {{ $product->available_quantity }}
                                </small>
                            </div>
                        </div>
                    </td>

                    <td>{{ $product->store?->name }}</td>

                    <td>
                        {{ $product->category?->name }}<br>
                        <small class="text-muted">
                            {{ $product->subCategory?->name }}
                        </small>
                    </td>

                    <td>
                        ₹{{ $product->price }}
                        @if($product->discount_price)
                            <br>
                            <small class="text-success">
                                ₹{{ $product->discount_price }}
                            </small>
                        @endif
                    </td>

                    <td>
                        <span class="badge
                            @if($product->status_id == 1) bg-success
                            @elseif($product->status_id == 2) bg-warning
                            @else bg-danger @endif">
                            @if($product->status_id == 1) Active
                            @elseif($product->status_id == 2) Blocked
                            @else Deleted @endif
                        </span>
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <!-- View Button -->
                            <button class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#productDetailsModal{{ $product->id }}">
                                <i class="fa fa-eye"></i>
                            </button>

                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $product->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $products->links() }}
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Do you really want to delete this product?</p>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>

                    <form method="POST" id="deleteProductForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- MODALS --}}
    @foreach($products as $product)
    <div class="modal fade" id="productDetailsModal{{ $product->id }}">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Product Details: {{ $product->name }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- IMAGES --}}
                @if($product->images->count())
                    <div class="mb-3">
                        <h6>Product Images</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->images as $img)
                                <img src="{{ asset('assets/product_images/'.$img->image) }}"
                                     style="width:70px;height:70px;
                                            object-fit:cover;
                                            border-radius:6px;
                                            border:1px solid #ccc;">
                            @endforeach
                        </div>
                    </div>
                @endif

                <table class="table table-bordered">
                    <tr><th>Name</th><td>{{ $product->name }}</td></tr>
                    <tr><th>Store</th><td>{{ $product->store?->name }}</td></tr>
                    <tr><th>Category</th><td>{{ $product->category?->name }}</td></tr>
                    <tr><th>Price</th><td>₹{{ $product->price }}</td></tr>
                    <tr><th>Discount Price</th><td>{{ $product->discount_price ?? '-' }}</td></tr>
                    <tr><th>Quantity</th><td>{{ $product->available_quantity }}</td></tr>
                    <tr><th>Delivery Available</th>
                        <td>{{ $product->delivery_available ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr><th>Description</th><td>{{ $product->description }}</td></tr>

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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteForm = document.getElementById('deleteProductForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            deleteForm.action = `/dashboard/admin/products/${productId}/delete`;
        });
    });
});
</script>

@endsection
