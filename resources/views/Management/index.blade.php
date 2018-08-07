@extends('layout.main')

@section('index-content')
<div id="wrapper">

    <!-- Navigation -->
    @include('Management.sidebar')

    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('Management.header')

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Dashboard</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="/index">Home</a>
                    </li>
                    <li class="active">
                        <strong>Dashboard</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Search Item</h5>
                            <!--<div class="ibox-tools">
                                <a href="" class="btn btn-primary btn-xs">Create new project</a>
                            </div>-->
                        </div>
                        <div class="ibox-content">
                            <div class="row m-b-sm m-t-sm">
                                <div class="col-md-1">
                                    <button type="button" onClick="history.go(0)" id="loading-example-btn" class="btn btn-white btn-sm" ><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                                <div class="col-md-11">
                                    <div class="input-group"><input type="text" onfocus="reprint(this)" onchange="reprint(this)" onblur="reprint(this)" onkeyup="remove(this)" id="typeahead_2" placeholder="Search" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="button" id="btn-search" class="btn btn-sm btn-primary"> Go!</button> </span></div>
                                </div>
                            </div>

                            <div class="project-list">
                                <div class="spiner-example" id="main-spinner" style="position: fixed;
    display: none;
    top: -50px;
    left: 0;
    right: 0;
    z-index: 9999; /* Specify a stack order in case you're using a different order for other elements */ padding: 23%; ">
                                    <div class="sk-spinner sk-spinner-wave">
                                        <div class="sk-rect1"></div>
                                        <div class="sk-rect2"></div>
                                        <div class="sk-rect3"></div>
                                        <div class="sk-rect4"></div>
                                        <div class="sk-rect5"></div>
                                    </div>
                                </div>
                                <table class="table table-hover">
                                    <tbody id="order-item">

                                    </tbody>
                                </table>

                                <!--<nav class='pagination' aria-label="...">
                                    <ul class="pager">
                                        <li><a href="#fh5co-explore" class='previous'>&lt;&lt;Previous</a></li>
                                        <span class='current'></span>
                                        <li><a href="#fh5co-explore" class='next'>Next&gt;&gt;</a></li>
                                    </ul>
                                </nav>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- /#page-wrapper -->
        <div class="footer">
            <div class="pull-right">
                <strong></strong>
            </div>
            <div>
                <strong>Copyright</strong> INFOZ-ITWORKS &copy; 2018
            </div>
        </div>
    </div>
</div>
<!-- /#wrapper -->

<!-- jQuery -->

@stop