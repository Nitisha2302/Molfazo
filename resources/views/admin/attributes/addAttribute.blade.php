@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Add Attributes</h2>
        </div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.attributes.store') }}"
                  method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label>Child Category</label>
                            <select name="child_category_id" class="form-control">
                                <option value="">Select Child Category</option>
                                @foreach($childCategories as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('child_category_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12 step-field">
                        <div class="form-group mb-4">
                            <label>Attributes JSON</label>
                            <textarea name="attributes_json"
                                      rows="6"
                                      class="form-control"
                                      placeholder='{"color":["Red","Blue"],"size":["M","L"]}'>{{ old('attributes_json') }}</textarea>
                            @error('attributes_json')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Save Attributes
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </section>
</div>
@endsection
