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
                             </span> <span class="text-muted text-xs block"> {{ Auth::user()->position }}  <b class="caret"></b></span> </span> </a>
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
            <li>{{--{!! (Request::is('inventory/create') ? 'class="active"' : '') !!}--}}
                <a href="javascript:;"><i class="fa fa-cubes"></i> <span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/inventory/create') }}">Create Item</a></li>
                    <li><a href="{{ url('/branch_inventory/create') }}">Branch Inventory </a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-briefcase"></i> <span class="nav-label">Purchase Order</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/po/create') }}" id="btn-Purchase-page">Create</a></li>
                    <li><a href="{{ url('/printpo') }}">Print</a></li>
                </ul>
            </li>
            <li>
                <a href="Receiving.php" id="btn-Receiving-page"><i class="fa fa-envelope"></i> <span class="nav-label">Receiving</span></a>

            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Sales Order</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/so/create') }}">Create</a></li>
                    <li><a href="Return" id="btn-Return-page">Return</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-cube"></i> <span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="Inventory_create">Create</a></li>
                    <li><a href="Inventory">Update</a></li>
                </ul>
            </li>
            <li>
                <a href="Sales_Report"><i class="fa fa-calendar"></i> <span class="nav-label">Sales Report</span></a>
            </li>
            <li>
                <a href="Incentive"><i class="fa fa-money"></i> <span class="nav-label">Incentive</span></a>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-user"></i> <span class="nav-label">Account </span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="create_mechanic">Create</a></li>
                    <li><a href="update_mechanic">Update</a></li>
                </ul>
            </li>

        </ul>

    </div>
</nav>