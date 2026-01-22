@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box ">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Child Categories</h2>

                <!-- Search & Filters -->
                <form method="GET"
                      action="{{ route('dashboard.admin.childcategories') }}"
                      class="d-flex gap-2 mb-3">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by child category name"
                           value="{{ request('search') }}">

                    <select name="sub_category_filter" class="form-control">
                        <option value="">All Sub Categories</option>
                        @foreach($subCategories as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('sub_category_filter') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status_filter" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->hasAny(['search','sub_category_filter','status_filter']))
                        <a href="{{ route('dashboard.admin.childcategories') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    @endif
                </form>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
            </div>

            <a href="{{ route('dashboard.admin.childcategories.create') }}"
               class="btn btn-green">
                Add Child Category
            </a>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered table-notification-list">
                <thead>
                    <tr>
                        <th>Child Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($childCategories as $child)
                        <tr>
                            <td>{{ $child->name }}</td>
                            <td>{{ $child->subCategory->name ?? 'N/A' }}</td>
                            <td>{{ $child->subCategory->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $child->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $child->status_id == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dashboard.admin.childcategories.edit', $child->id) }}"
                                       class="action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No child categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($childCategories->lastPage() > 1)
            <nav class="pt-3">
                {{ $childCategories->links() }}
            </nav>
        @endif
    </section>
</div>
@endsection
