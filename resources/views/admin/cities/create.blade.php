@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Add City</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

        <form action="{{ route('dashboard.admin.cities.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 step-field">
                    <label for="name">City Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <!-- <div class="col-md-6 step-field">
                    <label for="status">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status',1)==1?'selected':'' }}>Active</option>
                        <option value="0" {{ old('status')==0?'selected':'' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                </div> -->

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-dark">Add City</button>
                </div>
            </div>
        </form>

    </section>
</div>
@endsection