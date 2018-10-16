@if(Auth::user()->position!='Administrator')
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
                        <li><a href="/profile">Profile</a></li>
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
            <li>
                <a href="/home"><i class="fa fa-th-large"></i> <span class="nav-label">Home</span> </a>
            </li>
            <li>
                <a href="/employee"><i class="fa fa-user"></i> <span class="nav-label">Employee</span> </a>
            </li>
            <li>
                <a href="/branch/create"><i class="fa fa-street-view"></i> <span class="nav-label">Branch</span> </a>
            </li>
            <li>
                <a href="/logs"><i class="fa fa-lock"></i> <span class="nav-label">Activity Logs</span> </a>
            </li>

        </ul>

    </div>
</nav>