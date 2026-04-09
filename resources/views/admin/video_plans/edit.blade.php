@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Edit Video Plan</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.video-plans.update', $plan->id) }}"
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
       value="{{ $plan->name }}">
</div>
</div>

<!-- <div class="col-md-6">
<div class="form-group mb-4">
<label>Video Count</label>
<input type="number"
       name="video_count"
       class="form-control"
       value="{{ $plan->video_count }}">
</div>
</div> -->

<div class="col-md-6">
<div class="form-group mb-4">
<label>Duration (Days)</label>
<input type="number"
       name="duration"
       class="form-control"
       value="{{ $plan->duration_days }}">
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Price</label>
<input type="text"
       name="price"
       class="form-control"
       value="{{ $plan->price }}">
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Update Video Plan
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection