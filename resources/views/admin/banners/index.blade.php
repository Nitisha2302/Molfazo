@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

    <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
        <div class="heading-content-box">
            <h2>Banners</h2>

            {{-- Search --}}
            <form method="GET" action="{{ route('dashboard.admin.banners.index') }}" class="d-flex gap-2 mb-3">
                <input type="text" name="search" class="form-control" style="width:220px"
                       placeholder="Search by title" value="{{ request('search') }}">
               <select name="city" class="form-control" style="width:200px">
                    <option value="">All Cities</option>

                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-dark">Search</button>

                @if(request()->has('search'))
                    <a href="{{ route('dashboard.admin.banners.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>

            <a href="{{ route('dashboard.admin.banners.create') }}" class="btn btn-dark mb-3">Add New Banner</a>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>

    <div class="project-ongoing-box">
        <table class="table table-striped table-bordered table-notification-list">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($banners as $banner)
               
                <tr>
                    <td>
                        <img src="{{ $banner->image ? asset('assets/banner_images/'.$banner->image) : asset('assets/no-image.png') }}"
                          style="width:50px;height:50px;object-fit:cover;">

                    </td>
                    <td>{{ $banner->title ?? '-' }}</td>
                    <td>
                        @if($banner->cities)
                            @php
                                $cityNames = DB::table('cities')
                                            ->whereIn('id', $banner->cities)
                                            ->pluck('name')
                                            ->toArray();
                            @endphp
                            {{ implode(', ', $cityNames) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $banner->status == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $banner->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('dashboard.admin.banners.edit', $banner->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $banner->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteBannerModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No banners found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $banners->links() }}
    </div>

    <!-- Delete Banner Modal -->
    <div class="modal fade" id="deleteBannerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Do you really want to delete this banner?</p>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>

                    <form method="POST" id="deleteBannerForm">
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
    const deleteForm = document.getElementById('deleteBannerForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bannerId = this.getAttribute('data-id');
            deleteForm.action = `/dashboard/admin/banners/${bannerId}/delete`;
        });
    });
});
</script>
@endsection
