@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <h2>Store Details: {{ $store->name }}</h2>

        <div class="row mt-3">

            {{-- LEFT SIDE: LOGO --}}
            <div class="col-md-4 text-center mb-3">
                @php
                    $logo = $store->logo 
                        ? asset('assets/store_logo/' . $store->logo) 
                        : asset('assets/profile_image/default.png');
                @endphp

                <img src="{{ $logo }}" 
                     class="img-fluid rounded shadow"
                     style="max-height:200px;object-fit:cover;">
            </div>

            {{-- RIGHT SIDE: DETAILS --}}
            <div class="col-md-8">
                <table class="table table-bordered">

                    <tr><th>Name</th><td>{{ $store->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $store->email }}</td></tr>
                    <tr><th>Mobile</th><td>{{ $store->mobile }}</td></tr>
                    <!-- <tr><th>Country</th><td>{{ $store->country }}</td></tr> -->
                    <tr><th>City</th><td>{{ $store->city }}</td></tr>
                    <tr><th>Address</th><td>{{ $store->address }}</td></tr>

                    <tr>
                        <th>Type</th>
                        <td>
                            @if($store->type == 1) Retail
                            @elseif($store->type == 2) Online
                            @else Wholesale
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge 
                                @if($store->status_id == 1) bg-success
                                @elseif($store->status_id == 2) bg-warning
                                @else bg-danger @endif">
                                @if($store->status_id == 1) Active
                                @elseif($store->status_id == 2) Pending
                                @else Rejected @endif
                            </span>
                        </td>
                    </tr>

                    <tr><th>Working Hours</th><td>{{ $store->working_hours }}</td></tr>
                    <tr><th>Description</th><td>{{ $store->description }}</td></tr>

                </table>
            </div>
        </div>

        {{-- GOVERNMENT DOCUMENTS --}}
        @php
            $govDocs = $store->government_id 
                ? json_decode($store->government_id, true) 
                : [];
        @endphp

        <div class="mt-4">
            <h5>Government Documents</h5>

            @if(count($govDocs))
                <div class="d-flex flex-wrap gap-2">
                    @foreach($govDocs as $doc)
                        @php
                            $docUrl = asset('assets/store_documents/' . $doc);
                            $ext = pathinfo($doc, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                            <a href="{{ $docUrl }}" target="_blank">
                                <img src="{{ $docUrl }}"
                                     style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">
                            </a>
                        @else
                            <a href="{{ $docUrl }}" target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-file-pdf"></i> View PDF
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted">No documents uploaded</p>
            @endif
        </div>

        {{-- APPROVE / REJECT --}}
        @if($store->status_id == 2)
            <div class="mt-4">

                {{-- APPROVE --}}
                <form method="POST" action="{{ route('dashboard.admin.stores.approve', $store->id) }}">
                    @csrf
                    <button class="btn btn-success mb-3">Approve</button>
                </form>

                {{-- REJECT --}}
                <form method="POST" action="{{ route('dashboard.admin.stores.reject', $store->id) }}">
                    @csrf

                    <textarea name="reject_reason"
                              placeholder="Enter rejection reason..."
                              class="form-control mb-2"
                              rows="3"
                              required></textarea>

                    <button class="btn btn-danger">Reject</button>
                </form>

            </div>
        @elseif($store->status_id == 1)
            <span class="badge bg-success mt-3">Approved</span>
        @else
            <div class="mt-3">
                <span class="badge bg-danger">Rejected</span>
                <p class="mt-2"><strong>Reason:</strong> {{ $store->reject_reason }}</p>
            </div>
        @endif

        <a href="{{ route('dashboard.admin.notifications.index') }}" 
           class="btn btn-secondary mt-4">
           Back to Notifications
        </a>

    </section>
</div>
@endsection