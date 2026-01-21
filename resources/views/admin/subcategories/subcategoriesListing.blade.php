@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Sub-Categories</h2>

                <!-- Search + Filters -->
                <form method="GET" action="{{ route('dashboard.admin.subcategories') }}" class="d-flex gap-2 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by sub-category name" value="{{ request('search') }}">

                    <select name="category_filter" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category_filter') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <select name="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->hasAny(['search','category_filter','status_filter']))
                        <a href="{{ route('dashboard.admin.subcategories') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
            </div>

            <a href="{{ route('dashboard.admin.subcategories.create') }}" class="btn btn-green">Add Sub-Category</a>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered table-notification-list">
                <thead>
                    <tr>
                        <th>Sub-Category</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subCategories as $sub)
                        <tr>
                            <td>{{ $sub->name }}</td>
                            <td>{{ $sub->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $sub->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $sub->status_id == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dashboard.admin.subcategories.edit', $sub->id) }}" class="action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- <button class="dropdown-item delete-btn-design d-flex justify-content-center"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $sub->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No sub-categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subCategories->lastPage() > 1)
            <nav class="pt-3">{{ $subCategories->links() }}</nav>
        @endif
    </section>
</div>

<!-- Delete Modal (same as categories) -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="delete-confirmation-popup-title">Are you sure?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Do you really want to delete this sub-category?</p>
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

<!-- @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const form = document.getElementById('deleteForm');
        form.action = `/dashboard/admin/sub-categories/${id}`;
    });
});
</script>
@endpush -->
