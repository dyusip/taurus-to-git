@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Import Sales Entry </h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Import Sales Entry</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>

            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Import Excel File <small>Fill in information available</small></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-wrench"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-user">
                                        <li><a href="#">Config option 1</a>
                                        </li>
                                        <li><a href="#">Config option 2</a>
                                        </li>
                                    </ul>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-7 b-r">
                                        @if ( Session::has('success') )
                                            <div class="alert alert-success alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                    <span class="sr-only">Close</span>
                                                </button>
                                                <strong>{{ Session::get('success') }}</strong>
                                            </div>
                                        @endif

                                        @if ( Session::has('error') )
                                            <div class="alert alert-danger alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                    <span class="sr-only">Close</span>
                                                </button>
                                                <strong>{{ Session::get('error') }}</strong>
                                            </div>
                                        @endif

                                        @if (count($errors) > 0)
                                            <div class="alert alert-danger">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                                <div>
                                                    @foreach ($errors->all() as $error)
                                                        <p>{{ $error }}</p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <form action="/sales_import" id="form-import" method="POST" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                Select Branch
                                                <select required name="branch_code" class="form-control">
                                                    <option value=""></option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label> Date</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="reqDate" name="so_date"
                                                           class="form-control" required value="{{ date('m/d/Y') }}">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>
                                            </div>

                                            Choose your xls/csv File : <input type="file" name="file" class="form-control">

                                            <button type="submit" class="btn btn-primary ladda-button ladda-button-import btn-md" data-style="zoom-in" style="margin-top: 3%">Submit</button>
                                        </form>

                                    </div>
                                    <div class="col-sm-5"><h4>Not a member?</h4>
                                        <p>You can create an account:</p>
                                        <p class="text-center">
                                            <!--<i class="fa fa-shopping-cart big-icon"></i>-->
                                        </p>
                                    </div>
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
@endsection
@push('styles')
<link href="{{ asset('css/plugins/ladda/ladda-themeless.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!--ladda-->
<script src="{{ asset('js/plugins/ladda/spin.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.jquery.min.js') }}"></script>
<script>
    $(document).on('submit','#form-import',function () {
        var l = $( '.ladda-button-import' ).ladda();
        l.ladda( 'start' );
    });
    $(function () {
        $('#datepicker').datepicker();
    });
    $('#reqDate').change(function () {
        ($(this).val() != "") ? $('#reqDate-error').hide() : $('#reqDate-error').show();
        ($(this).val() != "") ? $('#reqDate').removeClass('error') : $('#reqDate').addClass('error');
    });
</script>
@endpush