@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>AI Photo Plans</h2>

    <form method="GET"
          action="{{ route('dashboard.admin.ai-photo-plans.index') }}"
          class="d-flex gap-2 mb-3">

        <input type="text"
               name="search"
               class="form-control"
               style="width:220px"
               placeholder="Search by name"
               value="{{ request('search') }}">

        <button class="btn btn-dark">Search</button>

        @if(request()->has('search'))
            <a href="{{ route('dashboard.admin.ai-photo-plans.index') }}"
               class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.admin.ai-photo-plans.create') }}"
       class="btn btn-dark mb-3">Add New Plan</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>

<div class="project-ongoing-box">
<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>Name</th>
    <th>Credits</th>
    <th>Price</th>
    <th>Per Photo</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($plans as $plan)
<tr>
    <td>{{ $plan->name }}</td>
    <td>{{ $plan->credits }}</td>
    <td>c. {{ number_format($plan->price, 2) }}</td>
    <td>c. {{ number_format($plan->price / max(1, $plan->credits), 2) }}</td>
    <td>
        @if($plan->is_active)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Hidden</span>
        @endif
    </td>
    <td>
        <div class="d-flex align-items-center gap-2">

            <a href="{{ route('dashboard.admin.ai-photo-plans.edit', $plan->id) }}"
               class="btn btn-info btn-sm" title="Edit">
                <i class="fa fa-edit"></i>
            </a>

            <form method="POST"
                  action="{{ route('dashboard.admin.ai-photo-plans.toggle', $plan->id) }}">
                @csrf
                <button type="submit"
                        class="btn btn-sm {{ $plan->is_active ? 'btn-warning' : 'btn-success' }}"
                        title="{{ $plan->is_active ? 'Hide from sellers' : 'Show to sellers' }}">
                    <i class="fa {{ $plan->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                </button>
            </form>

            <button class="btn btn-danger btn-sm delete-btn"
                    data-id="{{ $plan->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
                    title="Delete">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center">No plans found.</td>
</tr>
@endforelse
</tbody>
</table>

{{ $plans->links() }}
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
    <p>Do you really want to delete this plan?</p>
    <small class="text-muted">
        If sellers already bought this plan, it will be hidden instead of deleted
        so purchase history stays correct.
    </small>
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
            form.action = `/dashboard/admin/ai-photo-plans/${id}`;
        });
    });
});
</script>

@endsection