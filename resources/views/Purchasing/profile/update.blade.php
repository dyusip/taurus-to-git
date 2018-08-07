@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Profile</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index">Home</a>
                        </li>
                        <!--<li>
                            <a>Extra Pages</a>
                        </li>-->
                        <li class="active">
                            <strong>Profile</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <div class="wrapper wrapper-content">
                <div class="row animated fadeInRight">
                    <div class="col-md-4">
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
                    <div class="col-md-8">
                        <div class="ibox">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-2">Profile</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-3">Password</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-2" class="tab-pane active">
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
                                                        <input type="text" class="form-control" value="{{ Route::getFacadeRoot()->current()->uri() }}" name="name" id="name" placeholder="Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact">Contact</label>
                                                        <input type="text" class="form-control" value="{{ Auth::user()->contact }}" name="contact" id="contact" placeholder="Contact">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="text" class="form-control" value="{{ Auth::user()->email }}" readonly placeholder="Email">
                                                    </div>
                                                </div>
                                                <div class="mail-body text-right tooltip-demo">
                                                    <button class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Update Account"><i class="fa fa-sign-out"></i> Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="tab-3" class="tab-pane">
                                        <div class="panel-body">
                                            <h2>
                                                Change Password
                                            </h2>
                                            <br/>
                                            <!--<p>
                                                This example show how to use Steps with jQuery Validation plugin.
                                            </p>-->
                                            <!--<div class="hr-line-dashed"></div>-->
                                            <form id="form_change_pass" method="post" action="saving/change_password">
                                                <div class="ibox-content">
                                                    <div class="alert alert-success" hidden>
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="current_pass">Current Password</label>
                                                        <input type="password" required class="form-control" name="current_pass" id="current_pass" placeholder="Current Password">
                                                        <span class="help-block" style="display: none;">
                                                               <span class="glyphicon glyphicon-exclamation-sign"></span>                                                 </div>
                                                    <div class="form-group">
                                                        <label for="new_pass">New Password</label>
                                                        <input type="password" required class="form-control" name="new_pass" id="new_pass" placeholder="New Password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="confirm_pass">Confirm Password</label>
                                                        <input type="password" required class="form-control"  name="confirm_pass" id="confirm_pass" placeholder="Confirm Password">
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
            <div class="footer">
                <div class="pull-right">
                    10GB of <strong>250GB</strong> Free.
                </div>
                <div>
                    <strong>Copyright</strong> Example Company &copy; 2014-2015
                </div>
            </div>

        </div>
    </div>
@endsection
