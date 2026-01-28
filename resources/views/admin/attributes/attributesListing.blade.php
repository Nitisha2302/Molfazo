@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Category Attributes</h2>

                <form method="GET"
                      action="{{ route('dashboard.admin.attributes') }}"
                      class="d-flex gap-2 mb-3">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by child category"
                           value="{{ request('search') }}">

                    <button class="btn btn-dark">Filter</button>

                    @if(request('search'))
                        <a href="{{ route('dashboard.admin.attributes') }}"
                           class="btn btn-secondary">Reset</a>
                    @endif
                </form>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
            </div>

            <a href="{{ route('dashboard.admin.attributes.create') }}"
               class="btn btn-green">Add Attributes</a>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Child Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Attributes</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($attributes as $attr)
                        <tr>
                            <td>{{ $attr->childCategory->name }}</td>
                            <td>{{ $attr->childCategory->subCategory->name ?? 'N/A' }}</td>
                            <td>{{ $attr->childCategory->subCategory->category->name ?? 'N/A' }}</td>
                            <td>
                                <code>{{ json_encode($attr->attributes_json) }}</code>
                            </td>
                            <td>
                                <a href="{{ route('dashboard.admin.attributes.edit', $attr->id) }}"
                                   class="action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No attributes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attributes->lastPage() > 1)
            <nav class="pt-3">{{ $attributes->links() }}</nav>
        @endif

    </section>
</div>
@endsection
