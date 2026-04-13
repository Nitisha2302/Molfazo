@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

    <div class="ai-training-data-wrapper d-flex align-items-baseline justify-content-between">
        <div class="heading-content-box">
            <h2>All Queries</h2>

            {{-- FILTER --}}
            <form method="GET"
                action="{{ route('dashboard.admin.queries') }}"
                class="d-flex gap-2 mb-3">

                <input type="text" name="search"
                    class="form-control" style="width:220px"
                    placeholder="Search Mobile"
                    value="{{ request('search') }}">

                <select name="type" class="form-control" style="width:180px">
                    <option value="">All</option>
                    <option value="customer" {{ request('type')=='customer'?'selected':'' }}>Customer</option>
                    <option value="vendor" {{ request('type')=='vendor'?'selected':'' }}>Vendor</option>
                </select>

                <button class="btn btn-dark">Filter</button>

                @if(request()->hasAny(['search','type']))
                    <a href="{{ route('dashboard.admin.queries') }}"
                       class="btn btn-secondary">Reset</a>
                @endif
            </form>

            {{-- SUCCESS --}}
            <div id="successMsg" class="alert alert-success d-none"></div>

        </div>
    </div>

    <div class="project-ongoing-box">
        <table class="table table-striped table-bordered table-notification-list">
            <thead>
                <tr>
                    <th>Mobile</th>
                    <th>User</th>
                    <th>Title</th>
                    <th>Query</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($enquiries as $enquiry)
                <tr>
                    <td>{{ $enquiry->user->mobile ?? 'N/A' }}</td>

                    <td>
                        <strong>{{ $enquiry->user->name ?? 'N/A' }}</strong><br>
                        <small class="text-muted">
                            {{ $enquiry->type == 'vendor' ? 'Vendor' : 'Customer' }}
                        </small>
                    </td>

                    <td>{{ $enquiry->title }}</td>

                    <td>
                        <div style="max-width:250px; overflow:auto;">
                            {{ $enquiry->description }}
                        </div>
                    </td>

                    <td class="answer-cell">
                        {{ $enquiry->answer ?? 'N/A' }}
                    </td>

                    <td>
                        <span class="badge 
                            {{ $enquiry->status == 'answered' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($enquiry->status) }}
                        </span>
                    </td>

                    <td>{{ $enquiry->created_at->format('d M Y') }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-2">

                            {{-- Answer --}}
                            <button class="btn btn-primary btn-sm answer-btn"
                                data-id="{{ $enquiry->id }}">
                                Answer
                            </button>

                            {{-- Delete --}}
                            <button class="btn btn-danger btn-sm delete-btn"
                                data-id="{{ $enquiry->id }}">
                                <i class="fa fa-trash"></i>
                            </button>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No queries found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $enquiries->links() }}
    </div>

</section>
</div>

{{-- ANSWER MODAL --}}
<div class="modal fade" id="answerModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="answerForm">
                @csrf
                <input type="hidden" name="query_id" id="query_id">

                <div class="modal-header">
                    <h5 class="modal-title">Reply to Query</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea name="answer" id="answer"
                        class="form-control"
                        style="height:150px;"></textarea>

                    <span class="text-danger error"></span>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Submit</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Query</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this query?</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>

        </div>
    </div>
</div>

@endsection


{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function(){

    let deleteId = null;

    // OPEN ANSWER MODAL
    $('.answer-btn').click(function(){
        $('#query_id').val($(this).data('id'));
        $('#answer').val('');
        $('.error').text('');
        $('#answerModal').modal('show');
    });

    // SUBMIT ANSWER
    $('#answerForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('dashboard.admin.answer') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#answerModal').modal('hide');

                $('#successMsg')
                    .removeClass('d-none')
                    .text(res.success);

                setTimeout(() => location.reload(), 1000);
            },
            error: function(err){
                if(err.status === 422){
                    $('.error').text(err.responseJSON.errors.answer[0]);
                }
            }
        });
    });

    // OPEN DELETE MODAL
    $('.delete-btn').click(function(){
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // CONFIRM DELETE
    $('#confirmDeleteBtn').click(function(){

        if(!deleteId) return;

        $.ajax({
            url: "{{ route('dashboard.admin.delete') }}",
            method: "DELETE",
            data: {
                _token: "{{ csrf_token() }}",
                query_id: deleteId
            },
            success: function(){
                $('#deleteModal').modal('hide');

                $('#successMsg')
                    .removeClass('d-none')
                    .text('Query deleted successfully');

                setTimeout(() => location.reload(), 1000);
            }
        });

    });

});
</script>