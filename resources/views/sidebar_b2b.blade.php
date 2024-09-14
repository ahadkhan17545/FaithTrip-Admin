@php
    $companyProfile = App\Models\CompanyProfile::where('user_id', Auth::user()->id)->first();
@endphp

<nav class="sidebar sidebar-bunker">
    <div class="sidebar-header">
        <a href="{{url('/')}}" class="sidebar-brand">
            @if($companyProfile && $companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                <img class="max-h-45" src="{{url($companyProfile->logo)}}" />
            @else
                <img class="max-h-45" src="{{ url('assets') }}/img/logo.svg" />
            @endif
        </a>
    </div>
    <div class="profile-element d-block align-items-center flex-shrink-0">
        <div class="avatar online mb-2">
            @if(Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                <img src="{{ url(Auth::user()->image) }}" class="img-fluid rounded-circle w-100 h-100"/>
            @else
                <img src="{{ url('assets') }}/img/user.jpg" class="img-fluid rounded-circle w-100 h-100"/>
            @endif
        </div>
        <div class="profile-text text-center" style="margin-left: 0px;">
            <h6 class="m-0">{{ Auth::user()->name }}</h6>
            @if($companyProfile && $companyProfile->name)
            <span class="text-muted">
                {{-- <i class="typcn typcn-media-record text-success"></i> --}}
                {{ $companyProfile->name }}
            </span>
            @endif
            <h6 class="mb-0 mt-3">Balance: {{ Auth::user()->balance }}</h6>
        </div>
    </div>
    <div class="sidebar-body">
        @php
            $currentRoute = request()->route()->getName();
        @endphp
        <nav class="sidebar-nav">
            <ul class="metismenu">
                <li class="@if($currentRoute == 'home') mm-active @endif">
                    <a class="text-capitalize" href="{{ url('/home') }}">
                        <i class="typcn typcn-zoom-outline"></i>
                        Search pad
                    </a>
                </li>
                <li class="@if(in_array($currentRoute, ['ViewAllBooking', 'ViewCancelBooking'])) mm-active @endif">
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                        <i class="typcn typcn-info-outline"></i> Booking Information
                    </a>
                    <ul class="nav-second-level">
                        <li class="@if($currentRoute == 'ViewAllBooking') mm-active @endif">
                            <a class="text-capitalize" href="{{url('view/all/booking')}}">
                                Booking List
                            </a>
                        </li>
                        <li class="@if($currentRoute == 'ViewCancelBooking') mm-active @endif">
                            <a class="text-capitalize" href="{{url('view/cancel/booking')}}">
                                Cancelled Booking List
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="@if(in_array($currentRoute, ['ViewIssuedTickets', 'ViewCancelledTickets'])) mm-active @endif">
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                        <i class="typcn typcn-ticket"></i> Ticket information
                    </a>
                    <ul class="nav-second-level">
                        <li class="@if($currentRoute == 'ViewIssuedTickets') mm-active @endif">
                            <a class="text-capitalize" href="{{url('view/issued/tickets')}}">
                                Issued Ticket List
                            </a>
                        </li>
                        <li class="@if($currentRoute == 'ViewCancelledTickets') mm-active @endif">
                            <a class="text-capitalize" href="{{url('view/cancelled/tickets')}}">
                                Cancelled Ticket List
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="@if($currentRoute == 'view/saved/passangers') mm-active @endif">
                    <a class="text-capitalize" href="{{ url('/view/saved/passangers') }}">
                        <i class="typcn typcn-user-outline"></i>
                        Saved Passangers
                    </a>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                        <i class="typcn typcn-chart-bar-outline"></i> Reports
                    </a>
                    <ul class="nav-second-level">
                        <li class="@if($currentRoute == 'FlightBookingReport') mm-active @endif">
                            <a class="text-capitalize" href="{{url('flight/booking/report')}}">
                                Flight Booking Report
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="@if(in_array($currentRoute, ['ViewBankAccounts', 'AddBankAccount', 'EditBankAccount', 'ViewMfsAccounts', 'AddMfsAccount', 'EditMfsAccount', 'CreateTopupRequest', 'ViewRechargeRequests'])) mm-active @endif">
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                        <i class="typcn typcn-credit-card "></i> Account Recharge
                    </a>
                    <ul class="nav-second-level">
                        <li class="@if(in_array($currentRoute, ['CreateTopupRequest'])) mm-active @endif">
                            <a class="text-capitalize" href="{{url('create/topup/request')}}">
                                Submit Topup Request
                            </a>
                        </li>
                        <li class="@if(in_array($currentRoute, ['ViewRechargeRequests'])) mm-active @endif">
                            <a class="text-capitalize" href="{{url('view/recharge/requests')}}">
                                View Transactions
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="@if($currentRoute == 'MyProfile') mm-active @endif">
                    <a class="text-capitalize" href="{{ url('/my/profile') }}">
                        <i class="typcn typcn-user-outline"></i>
                        My profile
                    </a>
                </li>
                <li class="@if($currentRoute == 'CompanyProfile') mm-active @endif">
                    <a class="text-capitalize" href="{{ url('/company/profile') }}">
                        <i class="typcn typcn-edit"></i>
                        Company Profile
                    </a>
                </li>
                <li>
                    <a class="text-capitalize" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="typcn typcn-location-arrow-outline"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
        {{-- <div class="mt-auto p-3">
            <a href="{{ route('logout') }}" class="btn btn-primary w-100"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <img class="me-2" src="{{ url('assets') }}/img/logout.png" />
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div> --}}
    </div>
</nav>
