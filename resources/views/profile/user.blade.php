@extends('master')

@section('content')

    <div class="tile">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-4">
                                <div>
                                    <h6 class="fs-17 fw-semi-bold mb-0">Profile</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="media m-1 ">
                                <div class="align-left p-1">

                                    @if(Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                                        <img src="{{ url(Auth::user()->image) }}" class="avatar avatar-xl rounded-circle img-border height-100 mb-2">
                                        <a href="{{url('remove/user/image')}}" class="profile-image">❌ Remove</a>
                                    @else
                                        <img src="{{ url('assets') }}/img/user.jpg" class="avatar avatar-xl rounded-circle img-border height-100 mb-2">
                                    @endif

                                </div>
                                <div class="media-body ms-3 mt-1">

                                    <form action="{{url('update/user/profile')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="image" class="col-form-label fw-bold justify-content-start d-flex">Profile Photo</label>
                                                    <input type="file" name="image" id="image" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-form-label fw-bold justify-content-start d-flex">Full Name</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{Auth::user()->name}}" placeholder="Full Name">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="email" class="col-form-label fw-bold justify-content-start d-flex">Email</label>
                                                    <input type="email" name="email" id="email" class="form-control" value="{{Auth::user()->email}}" placeholder="user@email.com" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="phone" class="col-form-label fw-bold justify-content-start d-flex">Phone</label>
                                                    <input type="text" name="phone" id="phone" class="form-control" value="{{Auth::user()->phone}}" placeholder="+8801">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <hr>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="curent_password" class="col-form-label fw-bold justify-content-start d-flex pt-0">Current Password</label>
                                                    <input type="text" name="curent_password" id="curent_password" class="form-control" placeholder="*******">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="new_password" class="col-form-label fw-bold justify-content-start d-flex pt-0">New Password</label>
                                                    <input type="text" name="new_password" id="new_password" class="form-control" placeholder="*******">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 text-center pt-4">
                                                <button type="submit" class="btn btn-success btn-rounded">Update Profile</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer_js')
    @if ($errors->any())
        <script>
            toastr.error("Wrong Current Password");
        </script>
    @endif
@endsection
