<nav class="navbar-custom-menu navbar navbar-expand-lg m-0">
    <div class="sidebar-toggle-icon d-lg-none" id="sidebarCollapse">
        sidebar toggle<span></span>
    </div>
    <div class="d-none" id="typed-strings"></div>
    <div class="navbar-icon d-flex">
        <ul class="navbar-nav flex-row align-items-center">
            <li class="nav-item dropdown notification user-header-menu">
                <a class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img class="img-fluid rounded-circle" src="{{ url('assets') }}/img/user.jpg" />
                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-header d-sm-none">
                        <a href class="header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                    </div>
                    <div class="user-header">
                        <div class="img-user">
                            <img src="{{ url('assets') }}/img/user.jpg" />
                        </div>
                        <h6>{{ Auth::user()->name }}</h6>
                        <span><a href="#" class="__cf_email__">{{ Auth::user()->email }}</a></span>
                    </div>
                    <a href="{{url('my/profile')}}" class="dropdown-item">
                        <i class="typcn typcn-user-outline"></i>
                        My profile
                    </a>
                    <a href="{{url('company/profile')}}" class="dropdown-item">
                        <i class="typcn typcn-edit"></i>
                        Edit company profile
                    </a>
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
