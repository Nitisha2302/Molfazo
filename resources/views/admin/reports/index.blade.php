@extends('admin.layouts.app')

@section('content')

<div class="main-box-content main-space-box">

<section class="project-doorbox">

<div class="heading-content-box">

    <h2>User Reports</h2>

    {{-- Search --}}
    <form method="GET"
          action="{{ route('dashboard.admin.reports.index') }}"
          class="d-flex gap-2 mb-3">

        <input type="text"
               name="search"
               class="form-control"
               style="width:250px"
               placeholder="Search by name or phone"
               value="{{ request('search') }}">

        <button class="btn btn-dark">
            Search
        </button>

        @if(request()->has('search'))

            <a href="{{ route('dashboard.admin.reports.index') }}"
               class="btn btn-secondary">
                Reset
            </a>

        @endif

    </form>

    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif

</div>

<div class="project-ongoing-box">

<table class="table table-striped table-bordered table-notification-list">

<thead>

<tr>


    <th>Reported By</th>
    <th>Reporter Phone</th>

    <th>Reported User</th>
    <th>Reported User Phone</th>

    <th>Description</th>

    <th>Date</th>

    <th>Action</th>

</tr>

</thead>

<tbody>

@forelse($reports as $report)

<tr>

    

    {{-- Reporter Name --}}
    <td>
        {{ $report->user->name ?? 'N/A' }}
    </td>

    {{-- Reporter Phone --}}
    <td>
        {{ $report->user->mobile ?? 'N/A' }}
    </td>

    {{-- Reported User Name --}}
    <td>
        {{ $report->reportedUser->name ?? 'N/A' }}
    </td>

    {{-- Reported User Phone --}}
    <td>
        {{ $report->reportedUser->mobile ?? 'N/A' }}
    </td>

    {{-- Description --}}
    <td style="max-width:300px;">
        {{ $report->description }}
    </td>

    {{-- Date --}}
    <td>
        {{ $report->created_at->format('d M Y h:i A') }}
    </td>

    {{-- Action --}}
    <td>

        <button class="btn btn-danger btn-sm delete-btn"
                data-id="{{ $report->id }}"
                data-bs-toggle="modal"
                data-bs-target="#deleteReportModal">

            <i class="fa fa-trash"></i>

        </button>

    </td>

</tr>

@empty

<tr>

    <td colspan="8" class="text-center">
        No reports found.
    </td>

</tr>

@endforelse

</tbody>

</table>

{{-- Pagination --}}
<div class="mt-3">
    {{ $reports->appends(request()->query())->links() }}
</div>

</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteReportModal">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header border-0">

    <h5 class="modal-title">
        Delete Report
    </h5>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="modal">
    </button>

</div>

<div class="modal-body">

    <p>
        Are you sure you want to delete this report?
    </p>

</div>

<div class="modal-footer border-0">

    <button class="btn btn-secondary"
            data-bs-dismiss="modal">

        Cancel

    </button>

    <form method="POST"
          id="deleteReportForm">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger">

            Yes, Delete

        </button>

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

    const deleteForm = document.getElementById('deleteReportForm');

    deleteButtons.forEach(button => {

        button.addEventListener('click', function () {

            const id = this.getAttribute('data-id');

            deleteForm.action = `/dashboard/admin/reports/${id}/delete`;
        });
    });

});

</script>

@endsection