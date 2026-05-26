@extends('admin.layouts.app')

@section('content')

<div class="main-box-content main-space-box">
    <section class="project-doorbox">

        <div class="heading-content-box">
            <h2>Change Password</h2>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="project-ongoing-box">
            <form class="employe-form"
                  action="{{ route('dashboard.admin.change.password.submit') }}"
                  method="POST">

                @csrf

                <div class="row">

                    <!-- Current Password -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">

                            <label for="current_password">
                                Current Password
                            </label>

                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control"
                                   placeholder="Enter current password">

                            @error('current_password')
                                <div class="text-danger error-message">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">

                            <label for="new_password">
                                New Password
                            </label>

                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   class="form-control"
                                   placeholder="Enter new password">

                            @error('new_password')
                                <div class="text-danger error-message">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 step-field">
                        <div class="form-group mb-4">

                            <label for="new_password_confirmation">
                                Confirm Password
                            </label>

                            <input type="password"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm new password">

                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-md-12">
                        <button type="submit"
                                class="btn-box btn-submt-user py-block justify-content-center ms-0 mt-3">
                            Update Password
                        </button>
                    </div>

                </div>

            </form>
        </div>

    </section>
</div>

@endsection