
<header class="no-print">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-2 col-xs-2">
                <div class="logo">
                    <a href="{{url('/dashboard')}}"><img src="/img/logo.png" alt="Logo"></a>

                </div>
                <nav class="main_nav">
                    <ul>
                        <li><a href="{{url('home')}}">Orders List</a></li>
                        <li><a href="{{url('report')}}">Reports</a></li>
                        @if (Auth::user()->type != 'employee')
                            <li><a href="{{url('/dashboard')}}">Admin Panel</a></li>
                        @endif
                    </ul>
                </nav>
            </div>


            <div class="col-md-6 col-sm-10 col-sm-offset-0 col-xs-10 col-xs-offset-0">
                <nav class="profile_nav">
                    <ul class="personal_info">
                        <li class="date_time"><data value=""></data></li>
                        <li class="profile">
                            <span class="profile_name">{{ Auth::user()->name }} <i class="icon-dropdown"></i></span>
                            <ul class="header_dropdown choose_user">
                                {{--<li><a href="#">Profile</a></li>--}}
                                <li> <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                        Log Out
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form></li>
                            </ul>
                        </li>
                        <li class="languages">
                            <span class="current_language">english <i class="icon-dropdown"></i></span>
                            <ul class="header_dropdown choose_language">
                                <li><a href="#">english</a></li>
                                {{--<li><a href="#">french</a></li>--}}
                            </ul>
                        </li>
                        <li class="mobile">
                            <a class="mobile_nav_button">
                                <span></span>
                                <span></span>
                                <span></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <ul class="mobile_nav">
        <li><a href="{{url('home')}}">Orders List</a></li>
        <li><a href="#">Reports</a></li>
        @if (Auth::user()->type != 'employee')
            <li><a href="{{url('/dashboard')}}">Admin Panel</a></li>
        @endif
    </ul>
</header>
