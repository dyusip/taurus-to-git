@if(Auth::user()->position!='SALESMAN')
    <script>window.location='{{ url('/home') }}'</script>
@endif
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                            <!--<img alt="image" class="img-circle" src="../src/img/profile_small.jpg" />-->
                             </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> {{ Auth::user()->name }} </strong>
                             </span> <span class="text-muted text-xs block"> {{ Auth::user()->position }} - {{ Auth::user()->branch_user->name }}  <b class="caret"></b></span> </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile">Profile</a></li>
                        <li><a href="contacts.html">Contacts</a></li>
                        <li><a href="mailbox.html">Mailbox</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a></li>

                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            <li>{{--{!! (Request::is('home') ? 'class="active"' : '') !!}--}}
                <a href="{{ url('/home') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Home</span> </a>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-cubes"></i> <span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/salesman/inventory') }}">View</a></li>
                    <!--<li><a href="{{ url('/sr/create') }}">Request</a></li>-->
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Sales Order</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/so/create') }}">Create</a></li>
                    <li><a href="{{ url('/sr/create') }}">Return</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ url('/transfer/create') }}"><i class="fa fa-mail-forward"></i> <span class="nav-label">Transfer Item</span> </a>
            </li>
            <li>
                <a href="{{ url('/transferred/list') }}"><i class="fa fa-envelope"></i> <span class="nav-label">Received </span></a>
            </li>
            <li>
                <a href="{{ url('/transfer_report') }}"><i class="fa fa-arrow-circle-right"></i> <span class="nav-label">Transfer Report</span></a>
            </li>
            <li>
                <a href="{{ url('/salesreport') }}"><i class="fa fa-calendar"></i> <span class="nav-label">Sales Report</span></a>
            </li>
            <li>
                <a href="{{ url('/salesreturn_report') }}"><i class="fa fa-retweet"></i> <span class="nav-label">Return Report</span></a>
            </li>
            <li>
                <a href="Incentive"><i class="fa fa-money"></i> <span class="nav-label">Incentive</span></a>
            </li>
            <li>
                <a href="{{ url('/mechanic') }}"><i class="fa fa-user"></i> <span class="nav-label">Account </span></a>
            </li>

        </ul>

    </div>
</nav>