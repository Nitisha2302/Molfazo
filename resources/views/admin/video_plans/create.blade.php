@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Add Video Plan</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.video-plans.store') }}"
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

<!-- <div class="col-md-6">
<div class="form-group mb-4">
<label>Video Count</label>
<input type="number"
       name="video_count"
       class="form-control"
       placeholder="Enter number of videos"
       value="{{ old('video_count') }}">

@error('video_count')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div> -->

<div class="col-md-6">
<div class="form-group mb-4">
<label>Duration (Days)</label>
<input type="number"
       name="duration"
       class="form-control"
       placeholder="Enter duration"
       value="{{ old('duration') }}">

@error('duration')
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
Add Video Plan
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection