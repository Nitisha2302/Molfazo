@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

    <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
        <div class="heading-content-box">
            <h2>Cities</h2>

            {{-- Search --}}
            <form method="GET" action="{{ route('dashboard.admin.cities.index') }}" class="d-flex gap-2 mb-3">
                <input type="text" name="search" class="form-control" style="width:220px"
                       placeholder="Search by city name" value="{{ request('search') }}">
                <button class="btn btn-dark">Search</button>

                @if(request()->has('search'))
                    <a href="{{ route('dashboard.admin.cities.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>

            <a href="{{ route('dashboard.admin.cities.create') }}" class="btn btn-dark mb-3">Add New City</a>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>

    <div class="project-ongoing-box">
        <table class="table table-striped table-bordered table-notification-list">
            <thead>
                <tr>
                    <th>Name</th>
                    <!-- <th>Status</th> -->
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($cities as $city)
                <tr>
                    <td>{{ $city->name }}</td>
                    <!-- <td>
                        <span class="badge {{ $city->status == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $city->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </td> -->
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('dashboard.admin.cities.edit', $city->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $city->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteCityModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No cities found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $cities->links() }}
    </div>

    <!-- Delete City Modal -->
    <div class="modal fade" id="deleteCityModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Do you really want to delete this city?</p>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>

                    <form method="POST" id="deleteCityForm">
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
    const deleteForm = document.getElementById('deleteCityForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const cityId = this.getAttribute('data-id');
            deleteForm.action = `/dashboard/admin/cities/${cityId}`;
        });
    });
});
</script>
@endsection