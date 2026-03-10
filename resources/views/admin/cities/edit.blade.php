@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Edit City</h2>
        </div>

        <form action="{{ route('dashboard.admin.cities.update', $city->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 step-field">
                    <label for="name">City Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $city->name) }}">
                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <!-- <div class="col-md-6 step-field">
                    <label for="status">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $city->status==1?'selected':'' }}>Active</option>
                        <option value="0" {{ $city->status==0?'selected':'' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                </div> -->

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-dark">Update City</button>
                </div>
            </div>
        </form>

    </section>
</div>
@endsection