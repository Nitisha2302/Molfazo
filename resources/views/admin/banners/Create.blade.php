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

@endsection
