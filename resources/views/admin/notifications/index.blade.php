@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">
        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Notifications</h2>

                <!-- Search & Filter -->
                <!-- <form method="GET" action="{{ route('dashboard.admin.notifications.index') }}" class="d-flex gap-2 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by title or vendor" value="{{ request('search') }}">

                    <select name="type_filter" class="form-control">
                        <option value="">All Types</option>
                        <option value="Store" {{ request('type_filter') == 'Store' ? 'selected' : '' }}>Store</option>
                        <option value="Product" {{ request('type_filter') == 'Product' ? 'selected' : '' }}>Product</option>
                      
                    </select>

                    <button type="submit" class="btn btn-dark">Filter</button>

                    @if(request()->has('search') || request()->has('type_filter') || request()->has('status_filter'))
                        <a href="{{ route('dashboard.admin.notifications.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form> -->

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="project-ongoing-box">
            <table class="table table-striped table-bordered table-notification-list">
                <thead>
                    <tr>
                        <th>Type</th>
                        <!-- <th>Name</th>
                        <th>Vendor</th> -->
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                   @forelse($notifications as $note)
                        @php
                            $data = json_decode($note->data, true) ?? [];

                            // Corrected notification types
                            $typeName = $note->notification_type == 12 ? 'Store'
                                        : ($note->notification_type == 21 ? 'Product' : 'Other');

                            // Determine related name
                            $relatedName = '-';
                            if ($typeName === 'Store' && isset($data['store_id'])) {
                                $relatedName = \App\Models\Store::find($data['store_id'])->name ?? '-';
                            } elseif ($typeName === 'Product' && isset($data['product_id'])) {
                                $relatedName = \App\Models\Product::find($data['product_id'])->name ?? '-';
                            }

                            // Optional: read/unread badge (if you want to keep it)
                            $badgeClass = $note->is_read ? 'bg-success' : 'bg-warning';
                            $badgeText = $note->is_read ? 'Read' : 'Unread';
                        @endphp
                        <tr>
                            <td>{{ $typeName }}</td>
                            <!-- <td>{{ $relatedName }}</td> -->
                            <!-- <td>{{ $note->user->name ?? 'N/A' }}</td> -->
                            <td>{{ $note->title }}</td>
                            <td>{{ Str::limit($note->description, 50) }}</td>
                            <td>{{ $note->created_at->format('d M Y H:i') }}</td>
                            <!-- <td>
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </td> -->
                           <td>
                            <a href="{{ route('dashboard.admin.notifications.show', $note->id) }}" class="btn btn-primary btn-sm">
                                View
                            </a>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($notifications->lastPage() > 1)
                <nav class="pt-3">
                    {{ $notifications->links() }}
                </nav>
            @endif
        </div>
    </section>
</div>
@endsection