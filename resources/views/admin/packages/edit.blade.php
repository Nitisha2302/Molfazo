@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Edit Promotion Package</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.packages.update', $package->id) }}"
      method="POST">
@csrf
@method('PUT')

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Title</label>
<input type="text"
       name="title"
       class="form-control"
       value="{{ $package->title }}">
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Review Count</label>
<input type="number"
       name="review_count"
       class="form-control"
       value="{{ $package->review_count }}">
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Price</label>
<input type="text"
       name="price"
       class="form-control"
       value="{{ $package->price }}">
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Update Package
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection