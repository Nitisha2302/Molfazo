@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Promotion Packages</h2>

    <!-- Search -->
    <form method="GET"
          action="{{ route('dashboard.admin.packages.index') }}"
          class="d-flex gap-2 mb-3">

        <input type="text"
               name="search"
               class="form-control"
               style="width:220px"
               placeholder="Search by title"
               value="{{ request('search') }}">

        <button class="btn btn-dark">Search</button>

        @if(request()->has('search'))
            <a href="{{ route('dashboard.admin.packages.index') }}"
               class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.admin.packages.create') }}"
       class="btn btn-dark mb-3">Add New Package</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>

<div class="project-ongoing-box">
<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>Title</th>
    <th>Reviews</th>
    <th>Price</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($packages as $package)
<tr>
    <td>{{ $package->title }}</td>
    <td>{{ $package->review_count }}</td>
    <td>{{ $package->price }}</td>

    <td>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard.admin.packages.edit', $package->id) }}"
               class="btn btn-info btn-sm">
                <i class="fa fa-edit"></i>
            </a>

            <button class="btn btn-danger btn-sm delete-btn"
                    data-id="{{ $package->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center">No packages found.</td>
</tr>
@endforelse
</tbody>
</table>

{{ $packages->links() }}
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header border-0">
    <h5 class="modal-title">Are you sure?</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <p>Do you really want to delete this package?</p>
</div>

<div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>

    <form method="POST" id="deleteForm">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
    </form>
</div>
</div>
</div>
</div>

</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.delete-btn');
    const form = document.getElementById('deleteForm');

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            let id = this.getAttribute('data-id');
            form.action = `/dashboard/admin/packages/${id}`;
        });
    });
});
</script>

@endsection