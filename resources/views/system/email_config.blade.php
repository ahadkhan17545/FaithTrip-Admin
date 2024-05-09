@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('update/email/config')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Mail Server Configuration</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="host" class="col-form-label fw-bold justify-content-start d-flex">Mail Host <i class="text-danger">*</i></label>
                                        <input type="text" name="host" @if($config) value="{{$config->host}}" @endif id="host" class="form-control" placeholder="https://" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="port" class="col-form-label fw-bold justify-content-start d-flex">Mail Port <i class="text-danger">*</i></label>
                                        <input type="text" name="port" @if($config) value="{{$config->port}}" @endif id="port" class="form-control" placeholder="465/587" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="email" class="col-form-label fw-bold justify-content-start d-flex">Mail Username <i class="text-danger">*</i></label>
                                        <input type="email" name="email" @if($config) value="{{$config->email}}" @endif id="email" class="form-control" placeholder="example@sample.com" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="password" class="col-form-label fw-bold justify-content-start d-flex">Mail Password <i class="text-danger">*</i></label>
                                        <input type="text" name="password" @if($config) value="{{$config->password}}" @endif id="password" class="form-control" placeholder="******" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="mail_from_name" class="col-form-label fw-bold justify-content-start d-flex">Mail From Name</label>
                                        <input type="text" name="mail_from_name" id="mail_from_name" class="form-control" placeholder="Company Name" @if($config) value="{{$config->mail_from_name}}" @endif>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="mail_from_email" class="col-form-label fw-bold justify-content-start d-flex">Mail From Address</label>
                                        <input type="text" name="mail_from_email" id="mail_from_email" class="form-control" placeholder="company@email.com" @if($config) value="{{$config->mail_from_email}}" @endif>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="encryption" class="col-form-label fw-bold justify-content-start d-flex">Encryption</label>
                                        <select class="form-control" name="encryption" id="encryption">
                                            <option value="0" @if($config->encryption == 0) selected @endif>None</option>
                                            <option value="1" @if($config->encryption == 1) selected @endif>TLS</option>
                                            <option value="2" @if($config->encryption == 2) selected @endif>SSL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Update Mail Config</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer_js')
    @if ($errors->any())
        <script>
            toastr.success("Nail Server Config Updated");
        </script>
    @endif
@endsection
