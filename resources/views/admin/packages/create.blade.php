@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Add Promotion Package</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.packages.store') }}"
      method="POST">
@csrf

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Title</label>
<input type="text"
       name="title"
       class="form-control"
       placeholder="Enter title"
       value="{{ old('title') }}">

@error('title')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Review Count</label>
<input type="number"
       name="review_count"
       class="form-control"
       placeholder="Enter number of reviews"
       value="{{ old('review_count') }}">

@error('review_count')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Price</label>
<input type="text"
       name="price"
       class="form-control"
       placeholder="Enter price"
       value="{{ old('price') }}">

@error('price')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Add Package
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection