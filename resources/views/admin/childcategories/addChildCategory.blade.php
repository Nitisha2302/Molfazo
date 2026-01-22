@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box ">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Add Child Category</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.childcategories.store') }}"
                  method="POST">
                @csrf

                <div class="row">

                    <!-- Sub Category -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="sub_category_id">Sub Category</label>
                            <select id="sub_category_id"
                                    name="sub_category_id"
                                    class="form-control">
                                <option value="">Select Sub Category</option>
                                @foreach($subCategories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ old('sub_category_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('sub_category_id')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Child Category Name -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="name">Child Category Name</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control"
                                   placeholder="Enter child category name"
                                   value="{{ old('name') }}">

                            @error('name')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Add Child Category
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </section>
</div>
@endsection
