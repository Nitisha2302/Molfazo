@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Edit AI Photo Plan</h2>
</div>

<div class="project-ongoing-box">

<div class="alert alert-info">
    Editing this plan does <strong>not</strong> change credits already purchased by sellers —
    every order keeps its own snapshot of the price and credits.
</div>

<form class="employe-form"
      action="{{ route('dashboard.admin.ai-photo-plans.update', $plan->id) }}"
      method="POST">
@csrf
@method('PUT')

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Plan Name</label>
<input type="text" name="name" class="form-control"
       value="{{ old('name', $plan->name) }}">
@error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Credits (number of photos)</label>
<input type="number" name="credits" class="form-control"
       value="{{ old('credits', $plan->credits) }}">
@error('credits')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Price (c.)</label>
<input type="text" name="price" class="form-control"
       value="{{ old('price', $plan->price) }}">
@error('price')<div class="text-danger">{{ $message }}</div>@enderror
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Update Plan
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection