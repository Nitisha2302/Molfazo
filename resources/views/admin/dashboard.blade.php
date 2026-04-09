

@extends('admin.layouts.app')

@section('content')
<div class="main-box-content main-space-box ">

      <section class="project-doorbox">
        <div class="heading-content-box">
            <h2>Dashboard</h2>

            <!-- <div class="alert alert-success" role="alert" id="success-message" style="display: none;">
            {{ session('success') }}
            </div> -->
            <div id="assigned-success-message" class="alert alert-success" style="display: none;"></div>

            @if (session('success'))
            <div class="alert alert-success" role="alert" id="success-message">
                {{ session('success') }}
            </div>
            @endif
            <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> -->
        </div>
        <div class="project-ongoing-box">

            <!-- ================= USERS SECTION ================= -->
            <h4 class="mb-3">User Overview</h4>
            <div class="row mb-4">

                <!-- Total Users -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-primary text-white p-3 rounded-circle">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $totalUsers }}</h4>
                                <small>Total Users</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-info text-white p-3 rounded-circle">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $customerCount }}</h4>
                                <small>Total Customers</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendors -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-dark text-white p-3 rounded-circle">
                                <i class="fa fa-user-tie"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $vendorCount }}</h4>
                                <small>Total Vendors</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- ================= VENDOR STATUS SECTION ================= -->
            <h4 class="mb-3">Vendor Status</h4>
            <div class="row">

                <!-- Approved Vendors -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-success text-white p-3 rounded-circle">
                                <i class="fa fa-check"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $approvedVendors }}</h4>
                                <small>Approved Vendors</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejected Vendors -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-danger text-white p-3 rounded-circle">
                                <i class="fa fa-times"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $rejectedVendors }}</h4>
                                <small>Rejected Vendors</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Vendors (Optional but recommended) -->
                <div class="col-xl-4 col-sm-6 info-card mb-20">
                    <div class="card shadow border-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="card-icon bg-warning text-white p-3 rounded-circle">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $pendingVendors ?? 0 }}</h4>
                                <small>Pending Vendors</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
           

      </section>  
    </div>
@endsection
<style>
    .card-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>





