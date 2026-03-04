@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Add Bank</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.banks.store') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Bank Name</label>
<input type="text"
       name="name"
       class="form-control"
       placeholder="Enter bank name"
       value="{{ old('name') }}">

@error('name')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Status</label>
<select name="status" class="form-control">
<option value="1">Active</option>
<option value="0">Inactive</option>
</select>
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Bank Logo</label>
<input type="file"
       name="logo"
       class="form-control"
       accept="image/*">
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Add Bank

</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection