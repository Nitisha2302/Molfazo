@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Seller Credit Balances</h2>

    <form method="GET"
          action="{{ route('dashboard.admin.ai-photo-balances') }}"
          class="d-flex gap-2 mb-3">

        <input type="text" name="search" class="form-control" style="width:250px"
               placeholder="Seller name or email"
               value="{{ request('search') }}">

        <button class="btn btn-dark">Search</button>

        <a href="{{ route('dashboard.admin.ai-photo-balances') }}"
           class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="project-ongoing-box">
<table class="table table-striped table-bordered table-notification-list">
<thead>
<tr>
    <th>Seller</th>
    <th>Current Balance</th>
    <th>Total Purchased</th>
    <th>Total Used</th>
    <th>Last Updated</th>
</tr>
</thead>

<tbody>
@forelse($balances as $row)
<tr>
    <td>
        {{ $row->vendor->name ?? 'Deleted user' }}<br>
        <small class="text-muted">{{ $row->vendor->email ?? '' }}</small>
    </td>
    <td><strong>{{ $row->balance }}</strong></td>
    <td>{{ $row->total_purchased }}</td>
    <td>{{ $row->total_used }}</td>
    <td><small>{{ $row->updated_at->format('d M Y H:i') }}</small></td>
</tr>
@empty
<tr><td colspan="5" class="text-center">No balances yet.</td></tr>
@endforelse
</tbody>
</table>

{{ $balances->links() }}
</div>

</section>
</div>
@endsection
