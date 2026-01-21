@extends('admin.layouts.app')

@section('content')

<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Add Sub-Category</h2>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div id="notificationMessage" class="alert d-none" role="alert"></div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.subcategories.store') }}"
                  method="POST">
                @csrf

                <div class="row">

                    <!-- Parent Category -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="category_id">Parent Category</label>
                            <select id="category_id"
                                    name="category_id"
                                    class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ old('category_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sub-Category Name -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="name">Sub-Category Name</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control"
                                   placeholder="Enter sub-category name"
                                   value="{{ old('name') }}">

                            @error('name')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label for="status_id">Status</label>
                            <select id="status_id"
                                    name="status_id"
                                    class="form-control">
                                <option value="1" {{ old('status_id', 1) == 1 ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="2" {{ old('status_id') == 2 ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>

                            @error('status_id')
                                <div class="text-danger error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Add Sub-Category
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </section>
</div>

@endsection
