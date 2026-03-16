@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">

    <section class="project-doorbox">

        <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
            <div class="heading-content-box">
                <h2>Attribute Requests</h2>

                @if(session('success'))
                <div class="alert alert-success">
                  {{ session('success') }}
                </div>
                @endif

            </div>
        </div>

        <div class="project-ongoing-box">

            <table class="table table-striped table-bordered table-notification-list">

                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Attribute</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($requests as $req)

                        <tr>

                           <td>{{ $req->vendor->name ?? 'N/A' }}</td>
                           <td>{{ $req->childCategory->name ?? 'N/A' }}</td>

                            <td>{{ $req->attribute_name }}</td>

                            <td>{{ $req->attribute_value }}</td>

                            <td>

                                <span class="badge
                                @if($req->status=='pending') bg-warning
                                @elseif($req->status=='approved') bg-success
                                @else bg-danger
                                @endif">

                                {{ ucfirst($req->status) }}

                                </span>

                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    @if($req->status!='approved')
                                    <form method="POST" action="{{ route('dashboard.admin.attribute.requests.approve',$req->id) }}">
                                        @csrf
                                        <button class="btn btn-dark btn-sm">Approve</button>
                                    </form>
                                    @endif

                                    @if($req->status!='rejected')
                                        <form method="POST" action="{{ route('dashboard.admin.attribute.requests.reject',$req->id) }}">
                                            @csrf
                                            <button class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>
                          <td colspan="7" class="text-center">No requests found</td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            @if ($requests->lastPage() > 1)
            <nav class="pt-3">
            {{ $requests->links() }}
            </nav>
            @endif

        </div>

    </section>

</div>
@endsection