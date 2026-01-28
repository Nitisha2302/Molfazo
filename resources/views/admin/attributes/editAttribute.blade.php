@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Edit Attributes</h2>
        </div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.attributes.update', $attribute->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">
                            <label>Child Category</label>
                            <select name="child_category_id" class="form-control">
                                @foreach($childCategories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ $attribute->child_category_id == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
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
          class="form-control"
          rows="6">{{ json_encode($attribute->attributes_json, JSON_PRETTY_PRINT) }}</textarea>

                            @error('attributes_json')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Update Attributes
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </section>
</div>
@endsection
