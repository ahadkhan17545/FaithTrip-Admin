<nav class="sidebar sidebar-bunker">
    <div class="sidebar-header">
        <a href="search.html" class="sidebar-brand">
            <img class="max-h-45" src="{{url('assets')}}/img/logo.svg" />
        </a>
    </div>
    <div class="profile-element d-block align-items-center flex-shrink-0">
        <div class="avatar online mb-2">
            <img src="{{url('assets')}}/img/user.jpg" class="img-fluid rounded-circle" />
        </div>
        <div class="profile-text text-center">
            <h6 class="m-0">Gail Lakin</h6>
            <span class="text-muted">
                <i class="typcn typcn-media-record text-success"></i>
                Administrator
            </span>
        </div>
    </div>
    <div class="sidebar-body">
        <nav class="sidebar-nav">
            <ul class="metismenu">
                <li class="mm-active">
                    <a class="text-capitalize" href="search.html">
                    <i class="typcn typcn-zoom-outline"></i>
                    Search pad
                    </a>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-info-outline"></i> Pnr information
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./pnr/pnr-list.html">
                        Pnr list
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./pnr/company-cancel-pnr.html">
                        Cancelled pnr list
                        </a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-ticket"></i> Ticket information
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./ticket/ticket-list.html">
                        Ticket list
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./ticket/ticket-cancelled.html">
                        Cancelled ticket list
                        </a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-chart-bar-outline"></i> Reports
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./report/pnr-report.html">
                        Pnr report
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./report/pnr-cancel-report.html">
                        Pnr cancellation report
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./report/ticket-report.html">
                        Ticket report
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./report/ticket-cancel-report.html">
                        Ticket cancellation report
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./report/transaction-summery.html">
                        Transaction summery
                        </a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-lock-open-outline"></i> User access
                    role
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./role/permission.html">
                        Permission
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./role/role.html">
                        Role
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./role/user.html">
                        User
                        </a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-plane-outline"></i> Airline setup
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./setup/gds-setting.html">
                        Gds setting
                        </a>
                    </li>
                    <li class>
                        <a class="text-capitalize" href="./setup/exclude-airline.html">
                        Exclude airline
                        </a>
                    </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow material-ripple" href="javascript:void(0);">
                    <i class="typcn typcn-cog-outline"></i> Application setting
                    </a>
                    <ul class="nav-second-level">
                    <li class>
                        <a class="text-capitalize" href="./setting/general-setting.html">
                        General setting
                        </a>
                    </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="mt-auto p-3">
            <form method="POST" action="#" class="d-inline">
                <input type="hidden" name="_token" value="YUfvHZppIQ0LjkFXAMfOAQxtsgATtEQAv39jN70z" />
                <button type="submit" class="btn btn-primary w-100">
                    <span>
                    <img class="me-2" src="{{url('assets')}}/img/logout.png" />
                    <span>Logout</span>
                    </span>
                </button>
            </form>
        </div>
    </div>
</nav>
