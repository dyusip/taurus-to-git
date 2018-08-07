<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0;">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary"><i class="fa fa-bars"></i> </a>
        </div>
        <a class='navbar-brand' href='{{url('login')}}'><!--<img src="../infoLogo.png" height='28' width='250'/>--><img src="../../img/tinuod.gif" style="height: 100%"/></a>
        <ul class="nav navbar-top-links navbar-right">

            <li class="dropdown">

                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw fa-lg" style="color:cornflowerblue"></i>
                    <small> {{ Auth::user()->name }}</small>
                    <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <!--                    <li>-->
                    <!--                        <a data-toggle="modal" href="#modalUserProfile">-->
                    <!--                            <i class="fa fa-user fa-fw"></i> <small>User Profile</small></a>-->
                    <!--                    </li>-->
                    <li>
                        <a data-toggle="modal" href="#modalSettings">
                            <i class="fa fa-gear fa-fw"></i>
                            <small>Settings</small></a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <i class="fa fa-sign-out fa-fw"></i>
                            <small>Logout</small></a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<div class="modal inmodal fade" id="modalSettings" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                {{--<h4 class="modal-title">Profile</h4>
                <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small>--}}
            </div>
            <div class="modal-body">
                <div class="row animated fadeInRight">
                    <div class="col-md-5">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Profile Detail</h5>
                            </div>
                            <div>
                                <div class="ibox-content no-padding border-left-right">
                                    <!--<img alt="image" class="img-responsive" src="../src/img/profile_big.jpg">-->
                                    <img alt="image" class="img-responsive" src="../../img/tinuod.gif" style="padding: 5%;">
                                </div>
                                <div class="ibox-content profile-content">
                                    <h4><strong>{{ Auth::user()->name }}</strong></h4>
                                    <p><strong>Position:</strong> {{ Auth::user()->position }}</p>
                                    <p><strong>Contact #:</strong> {{ Auth::user()->contact }}</p>
                                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="ibox">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-profile">Profile</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-password">Password</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-profile" class="tab-pane active">
                                        <div class="panel-body">
                                            <h2>
                                                Personal Information
                                            </h2>
                                            <br/>
                                            <!--<p>
                                                This example show how to use Steps with jQuery Validation plugin.
                                            </p>-->

                                            <!--<div class="hr-line-dashed"></div>-->
                                            <form id="form_change_pass" method="post" action="saving/update_profile">
                                                <div class="ibox-content">
                                                    <div class="alert alert-success" hidden>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly placeholder="Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact">Contact</label>
                                                        <input type="text" class="form-control" value="{{ Auth::user()->contact }}" name="contact" id="contact" placeholder="Contact">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="text" class="form-control" value="{{ Auth::user()->email }}" readonly name="email" id="email" placeholder="Contact">
                                                    </div>
                                                </div>
                                                <div class="mail-body text-right tooltip-demo">
                                                    <button class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Update Account"><i class="fa fa-sign-out"></i> Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="tab-password" class="tab-pane">
                                        <div class="panel-body">
                                            <h2>
                                                Change Password
                                            </h2>
                                            <br/>
                                            <!--<p>
                                                This example show how to use Steps with jQuery Validation plugin.
                                            </p>-->
                                            <!--<div class="hr-line-dashed"></div>-->
                                            <form id="form_change_pass" method="post" action="/update_password">
                                                {{ csrf_field() }}
                                                <div class="ibox-content">
                                                    <div class="alert alert-success" hidden>
                                                    </div>
                                                    <input type="hidden" name="route" id="route" value="{{ Route::getFacadeRoot()->current()->uri() }}">
                                                    <div class="form-group{{ $errors->has('current_pass') ? ' has-error' : '' }}">
                                                        <label for="current_pass">Current Password</label>
                                                        <input type="password" required class="form-control" name="current_pass" id="current_pass" placeholder="Current Password">
                                                        @if ($errors->has('current_pass'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('current_pass') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group{{ $errors->has('new_pass') ? ' has-error' : '' }}">
                                                        <label for="new_pass">New Password</label>
                                                        <input type="password" required class="form-control" name="new_pass" id="new_pass" placeholder="New Password">
                                                        @if ($errors->has('new_pass'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('new_pass') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group {{ $errors->has('confirm_pass') ? ' has-error' : '' }}">
                                                        <label for="confirm_pass">Confirm Password</label>
                                                        <input type="password" required class="form-control"  name="confirm_pass" id="confirm_pass" placeholder="Confirm Password">
                                                        @if ($errors->has('confirm_pass'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('confirm_pass') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mail-body text-right tooltip-demo">
                                                    <button class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Update Account"><i class="fa fa-sign-out"></i> Update</button>
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

            {{--<div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>--}}
        </div>
    </div>
</div>
