@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-9">
                    <h2>Branch Inventory Record</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Home</a>
                        </li>
                        <li class="active">
                            <strong>Branch Inventory Record</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-content">
                                {{ csrf_field() }}
                                <p>Show report by</p>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <form id="form-id" method="POST">
                                                {{ csrf_field() }}
                                            <div class="col-md-4"><select data-placeholder="Choose a Branch..." class="select2_demo_1 form-control"  tabindex="2" name="branch" id = "branch" required>
                                                    <option value="">Select Branch</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}" {{ (@$request->branch == $branch->code)?'selected':'' }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5" >
                                                <div class="col-sm-7">
                                                    <div class="form-group form-inline">
                                                       {{-- <label> Date</label>--}}
                                                        <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                        <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                            <input readonly placeholder="" type="text" id="reqDate" name="date"
                                                                   class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                            <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>

                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="input-group-btn"> <button type="submit" class="btn btn-primary">Go!</button> </span>

                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="ibox-content">
                                <button data-toggle="tooltip" title="Print" form="form-id" id="find" class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/bi_records/print">
                                    <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                </button>
                                <div class="table-responsive">
                                    <table width="100%" class="table table-striped table-bordered table-hover"
                                           id="dataTables-bi">
                                        <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Desc</th>
                                            <th>Cost</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="footer">
                <div class="pull-right">
                    <strong></strong>
                </div>
                <div>
                    <strong>Copyright</strong> INFOZ-ITWORKS © 2017
                </div>
            </div>

        </div>

    </div>
@endsection
<!-- datatable-->
<!-- Gritter -->
@push('styles')
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('/js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet">
<link href="{{ asset('/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/ladda/ladda-themeless.min.css' )}}" rel="stylesheet">

<style>
    /* Absolute Center Spinner */
    .loading {
        position: absolute;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /* Transparent Overlay */
    /*.loading:before {
        content: '';
        display: block;
        position: fixed;
        text-align: center;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }*/
    .overlay {
        z-index: 999;
        display: none;
        position:absolute;
        top: 10px;
        left: 25px;
        width: 86%;
        height:90%;
        background-color: rgba(255,255,255,255.3);
    }

    /* :not(:required) hides these rules from IE9 and below */
    .loading:not(:required) {
        /* hide "loading..." text */
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    /*Readonly*/
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

</style>
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!--ladda-->
<script src="{{ asset('js/plugins/ladda/spin.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        //$(".select2_demo_1").select2();
        $('.select2_demo_1').select2({
            matcher: function (params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                keywords=(params.term).split(" ");

                for (var i = 0; i < keywords.length; i++) {
                    if (((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1)
                        return null;
                }
                return data;
            }
        });
        $('#quantity').removeAttr('disabled');
        $('#div-qty').show();

        $(document).ready(function () {
            resizeChosen();
            jQuery(window).on('resize', resizeChosen);
        });
        function resizeChosen() {
            $(".select2-container").each(function () {
                $(this).attr('style', 'width: 100%');
            });
        }
        $('#modalAdd').on('shown.bs.modal', function () {
            $('.chosen-select', this).chosen('destroy').chosen();
        });
        function preventSubmission(e) {
            if (e.preventDefault) e.preventDefault();
            return false;
        }
        function find(){
            document.getElementById("form-id").action = "/bi_records/print";
            document.getElementById("form-id").submit();
        }
        var form = document.getElementById('form-id'),
            findButton = document.getElementById('find');

        if (form.addEventListener) {
            form.addEventListener("submit", preventSubmission);
            findButton.addEventListener("click", find);
        } else {
            form.attachEvent("submit", preventSubmission);
            findButton.attachEvent("click", find);
        }
        $(document).on('submit','#form-id',function (e) {
            e.preventDefault();
            var date = formatMonth($('#reqDate').val());
            var branch = $('#branch').val();
            if ($.fn.dataTable.isDataTable('#dataTables-bi')) {
                $('#dataTables-bi').DataTable().clear().destroy();
            }
           $('#dataTables-bi').DataTable({
                'processing': true,
                'serverSide': true,
                'ajax': "bi_records/"+date+"/"+branch,
                'columns': [
                    {data: 2, name: 'bi_replicates.bir_prod_code'},
                    {data: 10, name: 'inventories.name'},
                    {data: 11, name: 'inventories.desc'},
                    {data: 5, name: 'bi_replicates.bir_cost'},
                    {data: 6, name: 'bi_replicates.bir_quantity'},
                    {data: 4, name: 'bi_replicates.bir_price'},
                   /* {data: 15, name: 'action', orderable: false, searchable: false, 'class':'text-center'}*/
                ],
               'dom': '<"html5buttons"B>lTfgitp',
               'buttons': [
                   /* {extend: 'copy'},
                    {extend: 'csv'},*/
                   {extend: 'excel', title: 'Branch Inventory Record'},
                   {extend: 'pdf', title: 'Branch Inventory Record'},

                   {
                       extend: 'print',
                       customize: function (win) {
                           $(win.document.body).addClass('white-bg');
                           $(win.document.body).css('font-size', '10px');

                           $(win.document.body).find('table')
                               .addClass('compact')
                               .css('font-size', 'inherit');
                       }
                   }
               ]

           });
        });

    });
    function formatMonth(date) {
        var d = new Date(date);
        var month = Number(d.getMonth()) + 1;
        return d.getFullYear() + "-" + month + "-" + d.getDate();
    }
    $(function () {
        $('#datepicker').datepicker();
    });
    $('#reqDate').change(function () {
        ($(this).val() != "") ? $('#reqDate-error').hide() : $('#reqDate-error').show();
        ($(this).val() != "") ? $('#reqDate').removeClass('error') : $('#reqDate').addClass('error');
    });
</script>
@endpush
