@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Add AI Photo Plan</h2>
</div>

<div class="project-ongoing-box">
<form class="employe-form"
      action="{{ route('dashboard.admin.ai-photo-plans.store') }}"
      method="POST">
@csrf

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Plan Name</label>
<input type="text" name="name" class="form-control"
       placeholder="e.g. 10 Photos" value="{{ old('name') }}">
@error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Credits (number of photos)</label>
<input type="number" name="credits" class="form-control"
       placeholder="e.g. 10" value="{{ old('credits') }}">
@error('credits')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Price (c.)</label>
<input type="text" name="price" class="form-control"
       placeholder="e.g. 23.00" value="{{ old('price') }}">
@error('price')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Add Plan
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection
