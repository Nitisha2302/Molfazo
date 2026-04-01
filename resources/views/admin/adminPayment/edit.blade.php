@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
<section class="project-doorbox">

<div class="heading-content-box">
    <h2>Payment Settings</h2>
</div>

<div class="project-ongoing-box">

{{-- ✅ SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form class="employe-form"
      action="{{ route('dashboard.admin.payment.update') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div class="row">

<div class="col-md-6">
<div class="form-group mb-4">
<label>Account Name</label>
<input type="text"
       name="account_name"
       class="form-control"
       value="{{ old('account_name', $data->account_name ?? '') }}">

@error('account_name')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>Account Number</label>
<input type="text"
       name="account_number"
       class="form-control"
       value="{{ old('account_number', $data->account_number ?? '') }}">

@error('account_number')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>IFSC</label>
<input type="text"
       name="ifsc"
       class="form-control"
       value="{{ old('ifsc', $data->ifsc ?? '') }}">

@error('ifsc')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>UPI ID</label>
<input type="text"
       name="upi_id"
       class="form-control"
       value="{{ old('upi_id', $data->upi_id ?? '') }}">

@error('upi_id')
<div class="text-danger">{{ $message }}</div>
@enderror
</div>
</div>

<div class="col-md-6">
<div class="form-group mb-4">
<label>QR Code</label>
<input type="file"
       name="qr_code"
       class="form-control">

@error('qr_code')
<div class="text-danger">{{ $message }}</div>
@enderror

@if(isset($data->qr_code))
    <input type="hidden" name="old_qr" value="{{ $data->qr_code }}">
    <a href="{{ asset('assets/qr_codes/'.$data->qr_code) }}" target="_blank">
        <img src="{{ asset('assets/qr_codes/'.$data->qr_code) }}"
            style="width:80px;height:80px;object-fit:cover;border:1px solid #ddd;">
    </a>
@endif
</div>
</div>

<div class="col-md-12">
<button type="submit"
class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
Update Payment
</button>
</div>

</div>
</form>
</div>

</section>
</div>
@endsection