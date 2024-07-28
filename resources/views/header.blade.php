<nav class="navbar-custom-menu navbar navbar-expand-lg m-0">
    <div class="sidebar-toggle-icon d-lg-none" id="sidebarCollapse">
        sidebar toggle<span></span>
    </div>
    <div class="d-none" id="typed-strings"></div>
    <div class="navbar-icon d-flex">
        <ul class="navbar-nav flex-row align-items-center">
            <li class="nav-item notification user-header-menu">
                @php
                    $sabreGdsInfo = DB::table('sabre_gds_configs')->where('id', 1)->first();
                    if($sabreGdsInfo->is_production == 0){
                        echo "<span style='color: red; font-weight: 600; border: 2px solid red; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Sandbox</span>";
                    } else {
                        echo "<span style='color: green; font-weight: 600; border: 2px solid green; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Live</span>";
                    }
                @endphp
            </li>
            <li class="nav-item dropdown notification user-header-menu">
                <a class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                    @if(Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                        <img src="{{ url(Auth::user()->image) }}" class="img-fluid rounded-circle"/>
                    @else
                        <img src="{{ url('assets') }}/img/user.jpg" class="img-fluid rounded-circle"/>
                    @endif

                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-header d-sm-none">
                        <a href class="header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <div class="user-header">
                        <div class="img-user">

                            @if(Auth::user()->image && file_exists(public_path(Auth::user()->image)))
                                <img src="{{ url(Auth::user()->image) }}" />
                            @else
                                <img src="{{ url('assets') }}/img/user.jpg" />
                            @endif

                        </div>
                        <h6>{{ Auth::user()->name }}</h6>

                        @php
                            $companyProfile = App\Models\CompanyProfile::where('user_id', Auth::user()->id)->first();
                        @endphp

                        @if($companyProfile && $companyProfile->name)
                        <span>
                            <a href="#" class="__cf_email__">
                            {{ $companyProfile->name }}
                            </a>
                        </span>
                        @endif

                    </div>

                    @if(Auth::user()->user_type == 1)
                    <a href="{{url('my/profile')}}" class="dropdown-item">
                        <i class="typcn typcn-user-outline"></i>
                        My profile
                    </a>
                    <a href="{{url('company/profile')}}" class="dropdown-item">
                        <i class="typcn typcn-edit"></i>
                        Edit company profile
                    </a>
                    @endif

                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="typcn typcn-key-outline"></i>
                        Sign out
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>
        </ul>
    </div>
</nav>
