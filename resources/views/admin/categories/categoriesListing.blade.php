@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box ">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Categories</h2>

                <!-- Search -->
                <form method="GET" action="{{ route('dashboard.admin.categories') }}" class="d-flex gap-2 mb-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by category name"
                           value="{{ request('search') }}">

                        <select name="status_filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Inactive</option>
                        </select>

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->has('search'))
                        <a href="{{ route('dashboard.admin.categories') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form>

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <a href="{{ route('dashboard.admin.categories.create') }}" class="btn btn-green">
                Add Category
            </a>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered table-notification-list">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                @php
                                    $img = $category->image
                                        ? asset('assets/category_images/'.$category->image)
                                        : asset('assets/no-image.png');
                                @endphp

                                <a href="{{ $img }}" target="_blank">
                                    <img src="{{ $img }}"
                                        width="40"
                                        height="40"
                                        style="object-fit:cover;border-radius:6px;">
                                </a>
                            </td>

                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="badge {{ $category->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $category->status_id == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('dashboard.admin.categories.edit', $category->id) }}"
                                       class="action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- <button class="dropdown-item delete-btn-design d-flex justify-content-center"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $category->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($categories->lastPage() > 1)
            <nav class="pt-3">
                {{ $categories->links() }}
            </nav>
        @endif
    </section>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="delete-confirmation-popup-title">Are you sure?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Do you really want to delete this category?</p>
            </div>

            <div class="modal-footer border-0">
                <button class="btn" data-bs-dismiss="modal">Cancel</button>

                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

