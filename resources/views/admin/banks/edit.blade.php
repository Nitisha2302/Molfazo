@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Edit Bank</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.banks.update', $bank->id) }}"
      method="POST"
      enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Bank Name</label>
<input type="text"
       name="name"
       class="form-control"
       value="{{ $bank->name }}">
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Status</label>
<select name="status" class="form-control">
<option value="1" {{ $bank->status==1?'selected':'' }}>Active</option>
<option value="0" {{ $bank->status==0?'selected':'' }}>Inactive</option>
</select>
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Bank Logo</label>
<input type="file"
       name="logo"
       class="form-control">

<img src="{{ $bank->logo ? asset('assets/bank_images/'.$bank->logo) : asset('assets/no-image.png') }}"
style="width:50px;height:50px;margin-top:10px;">
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Update Bank
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection