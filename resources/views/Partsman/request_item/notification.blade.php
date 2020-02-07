@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Partsman.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Partsman.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Request Item</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{url('/home')}}">Home</a>
                        </li>
                        <li class="active">
                            <strong>Request Item</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <div class="spiner-example" id="main-spinner" hidden style="position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 150px;
    background-color: rgba(0,0,0,0.5); /* Black background with opacity */
    z-index: 9999; /* Specify a stack order in case you're using a different order for other elements */ padding: 23%; ">
                <div class="sk-spinner sk-spinner-wave">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <form method="post" name="myform" id="myform" action="/request_item/store">
                    {{csrf_field()}}
                    <input type="hidden" name="from_branch" id="from_branch" value="{{ Auth::user()->branch }}">
                    <input type="hidden" name="to_branch" id="to_branch">
                    <input type="hidden" name="tf_prepby" id="tf_prepby" value="{{ Auth::user()->username }}">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Branch Info <small>Fill in information available</small></h5>
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
                                            <div class="form-group">
                                                <label>Branch Name</label>
                                                <select name="rqh_code" id="rqh_code" class="form-control select2_demo_1" required>
                                                    <option value=""></option>
                                                    @foreach($requests as $request)
                                                        <option value="{{ $request->rqh_code }}">{{ $request->rqh_code ." - ".$request->req_from_branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Address</label> <input readonly type="text" value="" name="address" style="text-transform: uppercase" id="address" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Contact #</label> <input readonly type="text" value="" name="contact" style="text-transform: uppercase" id="contact" placeholder="Contact #" class="form-control"></div>
                                        </div>
                                        <div class="col-sm-5"><h4>Not a member?</h4>
                                            <p>You can create an account:</p>
                                            <p class="text-center">
                                                <i class="fa fa-shopping-cart big-icon"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Date and Term</h5>
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
                                            <div class="form-group">
                                                <label>Request Date</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="reqDate" name="req_date"
                                                           class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label>Delivery Date</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="tf_date" name="tf_date"
                                                           class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="col-sm-5"><h4>Not a member?</h4>
                                            <p>You can create an account:</p>
                                            <p class="text-center">
                                                <i class="fa fa-wrench big-icon"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--row-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <!--<span class="label label-primary pull-right">Inventory</span>-->
                                    <h5>Item(s)</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="alert alert-danger" hidden id="item-error">

                                    </div>
                                    <div class="table-responsive tooltip-demo">
                                        <button data-toggle="tooltip" formtarget="_blank" title="Print Request" id="btn-print" class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/request_item/print">
                                            <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                        </button>
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="22%">Name</th>
                                                <th data-hide="phone" width="6%">UOM</th>
                                                <th data-hide="phone" width="5%">BR Stocks</th>
                                                <th data-hide="phone" width="6%">CW Stocks</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="7%">Cost</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center" data-hide="phone,tablet" width="3%">#</th>
                                                {{--<th class="text-center tooltip-demo" width="3%"><span data-toggle="tooltip" title="Add more item"><a href="#myModal" data-toggle="modal" ><i class="fa fa-plus-circle"></i></a></span></th>--}}
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="22%">Name</th>
                                                <th data-hide="phone" width="6%">UOM</th>
                                                <th data-hide="phone" width="5%">BR Stocks</th>
                                                <th data-hide="phone" width="6%">CW Stocks</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="7%">Cost</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center" data-hide="phone,tablet" width="3%">#</th>
                                                {{--<th class="text-center tooltip-demo" width="3%"><span data-toggle="tooltip" title="Add more item"><a href="#myModal" data-toggle="modal" ><i class="fa fa-plus-circle"></i></a></span></th>--}}
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="button" id="val-status">

                                            <button type="submit" name="button" id="btn-submit" value="create" class="btn btn-primary ladda-button ladda-button-demo btn-sm" data-style="zoom-in"><i class="fa fa-thumbs-up"></i> Approve PO</button>
                                            <button type="submit" name="button" value="cancel" class="btn btn-warning btn-sm"><i class="fa fa-thumbs-down"></i> Disapprove PO</button>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Amount</label>&nbsp;
                                                <input class="form-control text-right" readonly id="totalAmount" value="{!! Number_Format(@$total,2) !!}" name="tf_amount" type="text">
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--row-->
                </form>
            </div>
            <!-- modal-->
            <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <i class="fa fa-laptop modal-icon"></i>
                            <h4 class="modal-title">Add Item</h4>
                            <small class="font-bold">Number of items to be added.</small>
                        </div>
                        <div class="modal-body">
                            <div class="form-group"><label>Number of items</label> <input type="text" id="count" placeholder="Enter number of items" class="form-control"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                            <button type="button" id="add-count" class="btn btn-primary">Add Items</button>
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
<link href="{{ asset('/css/plugins/chosen/chosen.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/ladda/ladda-themeless.min.css' )}}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/chosen/chosen.jquery.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
{{--<script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>--}}
<script src="{{ asset('js/plugins/ladda/spin.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.jquery.min.js') }}"></script>

<script>

    function datatable() {
        $('#dataTables-salesOrder').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "bSort" : true,
            buttons: [
                /* {extend: 'copy'},
                 {extend: 'csv'},*/
                {extend: 'excel', title: 'Employee Account'},
                {extend: 'pdf', title: 'Employee Account'},

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
    }


    $(document).ready(function () {
        //datatable();
        Choosen();
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "preventDuplicates": true,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "7000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    });
    function Choosen() {
        $(".select2_demo_1").select2();

        var config = {
            '.chosen-select': {},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 10},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
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

    }
    $(document).on('change', '#rqh_code', function (e) {
        e.preventDefault();
        var rqh_code = $(this).val();
        $.ajax({
            url: "request/"+rqh_code,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function (output) {
                var data = jQuery.parseJSON(output);
                $('#address').val(data.branch.address);
                $('#contact').val(data.branch.contact);
                $('#reqDate').val(data.header.req_date);
                $('#totalAmount').val(data.header.req_amount.toFixed(2));
                $('#to_branch').val(data.header.req_from);
                $('#main-spinner').fadeOut();
                $('tbody').html('');

                /*if ($.fn.dataTable.isDataTable('#dataTables-salesOrder')) {
                    $('#dataTables-salesOrder').DataTable().clear().destroy();
                }*/

                $.each(data.detail, function (index, value) {
                    $('tbody').append("<tr> " +
                        "<td><input class='form-control' readonly type='text' name='prod_code[]' id='code' value='"+ value.rqd_prod_code +"'>" +
                        "<input class='form-control' readonly type='hidden' name='prod_cost[]' id='prod_cost' value='"+ value.rqd_prod_cost +"'>"+
                        "<input class='form-control' readonly type='hidden' name='prod_srp[]' id='prod_srp' value='"+ value.rqd_prod_srp +"'>"+
                        "</td> " +
                        "<td><input class='form-control' readonly type='text' name='prod_name[]' id='prod_name' value='"+ value.rqd_prod_name +"'></td> " +
                        "<td><input class='form-control' readonly type='text' name='uom[]' id='uom' value='"+ value.rqd_prod_uom +"'></td> " +
                        "<td><input class='form-control' readonly type='text'  value='"+ value.br_qty +"'></td> " +
                        "<td><input class='form-control' readonly type='text'  value='"+ value.cw_qty +"'></td> " +
                        "<td><input class='form-control text-success' readonly type='text' name='qty[]' id='qty' value='"+ value.rqd_prod_qty +"'></td> " +
                        "<td><input class='form-control' readonly type='text' name='cost[]' id='cost' value='"+ value.rqd_prod_price.toFixed(2) +"'></td> " +
                        "<td><input class='form-control' readonly type='text' name='amount[]' id='amount' value='"+ value.rqd_prod_amount.toFixed(2) +"'></td> " +
                        "<td class='text-center tooltip-demo'><a id='remove-row' class='text-danger' data-toggle='tooltip' title='Remove item'><i class='fa fa-remove'></i></a></td> " +
                        "</tr>");
                });
               /* datatable();*/
            }
        });
    });
    /*$(document).on('submit','#myform',function () {
        var l = $( '.ladda-button-demo' ).ladda();
        l.ladda( 'start' );
    });*/
    $('body').delegate('#remove-row','click',function () {
        var tr = $('tbody tr').length;
        if(tr > 1){
            $(this).parent().parent().remove();
            $('.tooltip').hide();
            totalAmount();
        }else{
            $('#item-error').html('You cannot remove the first field');
            $('#item-error').show();
        }
    });
    @if (session('status'))
        $(document).ready(function () {
            toastr.success("{{ session('status') }}");
        });
    @endif
     $(document).on('submit','#myform',function () {
        $('#val-status').val($('#btn-submit').val());
        var l = $( '.ladda-button-demo' ).ladda();
        l.ladda( 'start' );
    });

</script>
@endpush