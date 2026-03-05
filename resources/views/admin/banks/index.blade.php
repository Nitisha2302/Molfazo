@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Banks</h2>

    {{-- Search --}}
    <form method="GET"
          action="{{ route('dashboard.admin.banks.index') }}"
          class="d-flex gap-2 mb-3">

        <input type="text"
               name="search"
               class="form-control"
               style="width:220px"
               placeholder="Search by bank name"
               value="{{ request('search') }}">

        <button class="btn btn-dark">Search</button>

        @if(request()->has('search'))
            <a href="{{ route('dashboard.admin.banks.index') }}"
               class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <a href="{{ route('dashboard.admin.banks.create') }}"
       class="btn btn-dark mb-3">Add New Bank</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>

<div class="project-ongoing-box">
<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>Logo</th>
    <th>Name</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($banks as $bank)
<tr>
    <td>
        <img src="{{ $bank->logo ? asset('assets/bank_images/'.$bank->logo) : asset('assets/no-image.png') }}"
             style="width:50px;height:50px;object-fit:cover;">
    </td>

    <td>{{ $bank->name }}</td>

    <td>
        <span class="badge {{ $bank->status == 1 ? 'bg-success' : 'bg-danger' }}">
            {{ $bank->status == 1 ? 'Active' : 'Inactive' }}
        </span>
    </td>

    <td>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard.admin.banks.edit', $bank->id) }}"
               class="btn btn-info btn-sm">
                <i class="fa fa-edit"></i>
            </a>

            <button class="btn btn-danger btn-sm delete-btn"
                    data-id="{{ $bank->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteBankModal">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </td>

    
</tr>
@empty
<tr>
    <td colspan="4" class="text-center">No banks found.</td>
</tr>
@endforelse
</tbody>
</table>

{{ $banks->links() }}
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteBankModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header border-0">
    <h5 class="modal-title">Are you sure?</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <p>Do you really want to delete this bank?</p>
</div>

<div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>

    <form method="POST" id="deleteBankForm">
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
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteForm = document.getElementById('deleteBankForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            deleteForm.action = `/dashboard/admin/banks/${id}/delete`;
        });
    });
});
</script>

@endsection