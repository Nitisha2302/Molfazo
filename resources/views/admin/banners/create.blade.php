@extends('admin.layouts.app')

@section('content')

<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Add Banner</h2>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div id="notificationMessage" class="alert d-none" role="alert"></div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.banners.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- Banner Title -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="title">Banner Title</label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="form-control"
                                   placeholder="Enter banner title"
                                   value="{{ old('title') }}">

                            @error('title')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                   <!-- Cities Select -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label>Select Cities</label>
                            <div class="city-scroll-box d-flex flex-wrap gap-2">

                                <!-- All Cities -->
                                <label class="city-pill">
                                    <input type="checkbox" id="allCities" value="all">
                                    <span>All Cities</span>
                                </label>

                                @foreach($cities as $city)

                                <label class="city-pill">
                                    <input type="checkbox" name="cities[]" value="{{ $city->id }}">
                                    <span>{{ $city->name }}</span>
                                </label>

                                @endforeach

                            </div>

                            @error('cities')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>

                    <!-- Link Type -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label>Select Type (Optional)</label>
                            <select id="linkType" name="link_type" class="form-control">
                                <option value="">None</option>
                                <option value="store">Store</option>
                                <option value="product">Product</option>
                            </select>
                        </div>
                    </div>

                    <!-- Store Dropdown -->
                    <!-- Store Selection -->
                    <div class="col-md-6 step-field d-none" id="storeDropdown">
                        <div class="form-group mb-4">
                            <label>Select Stores</label>

                            <div class="city-scroll-box d-flex flex-wrap gap-2">
                                <label class="city-pill">
                                    <input type="checkbox" id="allStores">
                                    <span>All Stores</span>
                                </label>

                                @foreach($stores as $store)

                                    <label class="city-pill">
                                        <input type="checkbox" name="link_ids[]" value="{{ $store->id }}">
                                        <span>{{ $store->name }}</span>
                                    </label>

                                @endforeach

                            </div>
                        </div>
                    </div>

                                        <!-- Product Dropdown -->
                                    <!-- Product Selection -->
                    <div class="col-md-6 step-field d-none" id="productDropdown">
                        <div class="form-group mb-4">
                            <label>Select Products</label>

                            <div class="city-scroll-box d-flex flex-wrap gap-2">
                                <label class="city-pill">
                                    <input type="checkbox" id="allProducts">
                                    <span>All Products</span>
                                </label>

                                @foreach($products as $product)

                                    <label class="city-pill">
                                        <input type="checkbox" name="link_ids[]" value="{{ $product->id }}">
                                        <span>{{ $product->name }}</span>
                                    </label>

                                @endforeach

                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="status">Status</label>
                            <select id="status"
                                    name="status"
                                    class="form-control">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>

                            @error('status')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Banner Image -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label>Banner Image</label>
                            <input type="file"
                                   name="image"
                                   id="bannerImage"
                                   class="form-control"
                                   accept="image/*">

                            @error('image')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror

                            <img id="preview" style="width:200px;height:100px;object-fit:cover;margin-top:10px;display:none;">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Add Banner
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </section>
</div>

<script>
document.getElementById('bannerImage').addEventListener('change', function(event){
    const [file] = event.target.files;
    if(file){
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>

<script>

document.getElementById('allCities').addEventListener('change', function(){

    let checkboxes = document.querySelectorAll('input[name="cities[]"]');

    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });

});

</script>

<script>
document.getElementById('linkType').addEventListener('change', function () {

    let type = this.value;

    document.getElementById('storeDropdown').classList.add('d-none');
    document.getElementById('productDropdown').classList.add('d-none');

    if (type === 'store') {
        document.getElementById('storeDropdown').classList.remove('d-none');
    }

    if (type === 'product') {
        document.getElementById('productDropdown').classList.remove('d-none');
    }
});

// ALL STORES
document.getElementById('allStores').addEventListener('change', function(){

    let checkboxes = document.querySelectorAll('#storeDropdown input[name="link_ids[]"]');

    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });

});

// ALL PRODUCTS
document.getElementById('allProducts').addEventListener('change', function(){

    let checkboxes = document.querySelectorAll('#productDropdown input[name="link_ids[]"]');

    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });

});
</script>

@endsection
