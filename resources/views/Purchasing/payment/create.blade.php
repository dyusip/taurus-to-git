@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Purchasing.sidebar')

        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>PO Payment</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>PO Payment</strong>
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
                <form method="post" name="myform" id="myform" action="/po">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Supplier Info <small>Fill in information available</small></h5>
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
                                            <div class="form-group"><label for="sup_code">PO #</label>
                                                <select class="form-control select2_demo_1" id="rec_code" name="rec_code">
                                                    <option></option>
                                                    @foreach($pos as $po)
                                                        @foreach($po->po_re_header as $receiving)
                                                            <option value="{{ $receiving->rh_no }}">{{ $po->po_code }} - {{ $receiving->rh_no }} - {{ $po->supplier->name }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="sup_code">SUPPLIER NAME</label>
                                                <input type="text" id="sup_name"  readonly style="text-transform: uppercase"  placeholder="NAME" class="form-control">
                                            </div>
                                            <div class="form-group"><label>ADDRESS</label> <input type="text" id="sup_address"  readonly style="text-transform: uppercase"  placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>CONTACT #</label> <input type="text" id="sup_contact" readonly style="text-transform: uppercase"  placeholder="Contact #" class="form-control"></div>
                                            <!--<div>
                                                   <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Log in</strong></button>
                                                   <label> <input type="checkbox" class="i-checks"> Remember me </label>
                                               </div>-->

                                        </div>
                                        <div class="col-sm-5"><h4>Not a member?</h4>
                                            <p>You can create an account:</p>
                                            <p class="text-center" style="margin-bottom: -3%">
                                                <i class="fa fa-money big-icon"></i>
                                            </p>
                                            <div class="form-group">
                                                <label>AMOUNT</label>
                                                <input type="text" id="amount"  readonly style="text-transform: uppercase"  placeholder="AMOUNT" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Date and Reference</h5>
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
                                                <label>SI #</label>
                                                <input type="text" id="si_no"  readonly style="text-transform: uppercase"  placeholder="SI #" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>PO DATE</label>
                                                <input type="text" id="po_date"  readonly style="text-transform: uppercase"  placeholder="PO DATE" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>REC DATE</label>
                                                <input type="text" id="rec_date"  readonly style="text-transform: uppercase"  placeholder="DATE RECEIVED" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>DUE DATE</label>
                                                <input type="text" id="due_date"  readonly style="text-transform: uppercase"  placeholder="DUE DATE" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-5"><h4>Not a member?</h4>
                                           {{-- <p>You can create</p>--}}
                                            <p class="text-center" style="margin-bottom: 17%;">
                                                <i class="fa fa-calendar-o big-icon"></i>
                                            </p>
                                            <div class="form-group">
                                                <label>TERM</label>
                                                <input type="text" id="term"  readonly style="text-transform: uppercase"  placeholder="TERM" class="form-control">
                                            </div>
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
                                    <h5>Payment Record</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="alert alert-danger" hidden id="item-error">

                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="7%">Payment #</th>
                                                <th data-hide="phone" width="9%">Date</th>
                                                <th data-hide="phone" width="8%">Payment Type</th>
                                                <th data-hide="phone" width="6%">Check #</th>
                                                <th data-hide="phone,tablet" width="8%">Check Date</th>
                                                <th class="text-center" width="8%">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="btn-group ">
                                                {{--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>--}}
                                                <button type="button" data-toggle="modal" id="btn-md" data-target="#Modal-Payment" class="btn btn-primary btn-sm" > Make a Payment</button>
                                            </div>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Paid</label>&nbsp;
                                                <input class="form-control text-right" readonly id="paid"  type="text">
                                            </div>
                                            <div class="clearfix"><br></div>
                                            <div class="form-inline">
                                                <label for="totalAmount">Remaining Balance</label>&nbsp;
                                                <input class="form-control text-right" readonly id="rem_bal"  type="text">
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
            <div class="modal inmodal" id="Modal-Payment" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <i class="fa fa-laptop modal-icon"></i>
                            <h4 class="modal-title">Add Payment</h4>
                            <small class="font-bold">Number of items to be added.</small>
                        </div>
                        <form method="post" id="payment_form">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="form-group">
                                    <fieldset>
                                        <p>PAYMENT TYPE</p>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio1" value="CASH" name="payment_type" checked="">
                                            <label for="inlineRadio1"> CASH </label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio2" value="CHECK" name="payment_type">
                                            <label for="inlineRadio2"> CHECK </label>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="row">
                                    <div class="col-xs-7 col-md-7">
                                        <div class="form-group">
                                            <label>CHECK #</label>
                                            <input type="text" class="form-control" name="check_no" id="check_no" placeholder="0123456789" required="">
                                        </div>
                                    </div>
                                    <div class="col-xs-5 col-md-5 pull-right">
                                        <div class="form-group">
                                            <label>CHECK DATE</label>
                                            <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                            <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                <input autocomplete="off" readonly placeholder="MM / YY" type="text" id="check_date" name="check_date"
                                                       class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group"><label>BANK NAME</label> <input style="text-transform: uppercase" type="text" id="bank_name" name="bank_name" placeholder="TAURUS BANK" class="form-control"></div>
                                {{--<div class="form-group"><label>AMOUNT</label> <input type="text" id="count" placeholder="10,000" class="form-control"></div>--}}
                                <div class="row">
                                    <div class="col-xs-7 col-md-7">
                                        <div class="form-group">
                                            <label>AMOUNT</label>
                                            <input type="text" autocomplete="off" class="form-control" name="payment_amount" id="payment_amount" placeholder="10,000" required="">
                                        </div>
                                    </div>
                                    <div class="col-xs-5 col-md-5 pull-right">
                                        <div class="form-group">
                                            <label>PAYMENT DATE</label>
                                            <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                            <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker1'>
                                                <input autocomplete="off" placeholder="" type="text" id="payment_date" name="payment_date"
                                                       class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                <button type="submit" id="add-count" class="btn btn-primary">Add Payment</button>
                            </div>
                        </form>
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

@endsection
@push('styles')
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/ladda/ladda-themeless.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">

@endpush
@push('scripts')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('/js/plugins/typehead/bootstrap3-typehead.min.js') }}"></script>
<!-- Data picker -->
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>



<script>
    $(document).ready(function () {
        disabled();
        $('[id^=payment_amount]').keypress(validateNumber);
        $(".select2_demo_1").select2();

        $(document).ready(function () {
            resizeChosen();
            jQuery(window).on('resize', resizeChosen);
        });
        function resizeChosen() {
            $(".select2-container").each(function () {
                $(this).attr('style', 'width: 100%');
            });
        }
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
    $(document).on('change','#rec_code',function (e) {
        e.preventDefault();
        var rec = $(this).val();
        $.ajax({
            url: "payment/"+rec,
            beforeSend: function () {
                $('#main-spinner').fadeIn();
            },
            success: function (output) {
                var data = jQuery.parseJSON(output);
                $('#sup_name').val(data.supplier.name);
                $('#sup_address').val(data.supplier.address);
                $('#sup_contact').val(data.supplier.contact);
                $('#si_no').val(data.header.rh_si_no);
                $('#rec_no').val(data.header.rh_no);
                $('#po_date').val(dateFormat(data.header.re_po_header.po_date));
                $('#rec_date').val(dateFormat(data.header.rh_date));
                $('#term').val(data.header.re_po_header.term);
                $('#amount').val(data.amount.total_amount);
                $('#due_date').val(data.due_date);
                $('#order-tbl').html('');
                var totalpaid = 0;
                $.each(data.detail, function (index, value) {
                    var check_no = value.pd_checkno==""?'N/A':value.pd_checkno;
                    var check_date = value.pd_checkno==""?'N/A':value.pd_checkdate;
                    $('#order-tbl').append(" <tr> " +
                        "<td>"+ value.pd_paymentno +"</small></td> " +
                        "<td >"+ value.pd_date +" </td> " +
                        "<td >"+ value.pd_type +"</td> " +
                        "<td >"+ check_no +"</td> " +
                        "<td >"+ check_date +"</td> " +
                        "<td >"+ value.pd_amount +"</td> " +
                        "</tr>");
                    totalpaid += Number(value.pd_amount);
                });
                $('#paid').val(totalpaid);
                $('#rem_bal').val(data.rem_bal);
                $('#btn-md').removeAttr('disabled').html('Make a Payment');
                if(data.rem_bal<=0){
                    $('#btn-md').attr('disabled',true).html('Fully Paid');
                }
                $('#main-spinner').fadeOut();

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " " + thrownError);
            }
        });
    });
    $(document).on('submit','#payment_form',function (e) {
        e.preventDefault();
        var rec = $('#rec_code').val();
        var amt = $('#amount').val();
        if(rec !=="") {
            $.ajax({
                url: "payment/",
                type: "POST",
                data: $(this).serialize() + '&ph_rh_no=' + rec + '&ph_amount=' + amt,
                beforeSend: function () {
                    $('#main-spinner').fadeIn();
                },
                success: function (output) {
                    var data = jQuery.parseJSON(output);
                    $('#order-tbl').html('');
                    var totalpaid = 0;
                    $.each(data.detail, function (index, value) {
                        var check_no = value.pd_checkno == "" ? 'N/A' : value.pd_checkno;
                        var check_date = value.pd_checkno == "" ? 'N/A' : value.pd_checkdate;
                        $('#order-tbl').append(" <tr> " +
                            "<td>" + value.pd_paymentno + "</small></td> " +
                            "<td >" + value.pd_date + " </td> " +
                            "<td >" + value.pd_type + "</td> " +
                            "<td >" + check_no + "</td> " +
                            "<td >" + check_date + "</td> " +
                            "<td >" + value.pd_amount + "</td> " +
                            "</tr>");
                        totalpaid += Number(value.pd_amount);
                    });
                    $('#paid').val(totalpaid);
                    $('#rem_bal').val(data.header.ph_rembal);
                    $('#payment_form input:text ').val('');
                    $('input:radio[value=CASH]').prop('checked', true);
                    disabled();
                    $('#payment_date').val("{{ \Carbon\Carbon::now()->format('m/d/Y') }}");
                    if (data.header.ph_rembal <= 0) {
                        $('#btn-md').attr('disabled', true).html('Fully Paid');
                        $('#Modal-Payment').modal('hide');
                    }
                    $('#main-spinner').fadeOut();

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + " " + thrownError);
                }
            });
        }else{
            toastr.error("Please select PO# to make payment");
        }
    });
    function dateFormat(dt) {
        var d = new Date(dt);
        var month = (Number(d.getMonth()+1) < 10)?"0"+Number(d.getMonth()+1):Number(d.getMonth()+1);
        var day = (d.getDate() < 10)?"0"+d.getDate():d.getDate();
        return month+"/"+day+"/"+d.getFullYear();
    }
    $('input:radio[name=payment_type]').change(function () {
        var btn = $(this).val();
        if(btn=='CASH'){
            disabled();
        }else if(btn=='CHECK'){
            $('#check_date').removeAttr('readonly');
            $('#check_no').removeAttr('readonly');
            $('#bank_name').removeAttr('readonly');
        }
    });
    function disabled() {
        $('#check_date').attr('readonly',true).val('');
        $('#check_no').attr('readonly',true).val('');
        $('#bank_name').attr('readonly',true).val('');
    }
</script>
<script type="text/javascript">
    $(function () {
        $('#datepicker').datepicker();
        $('#datepicker1').datepicker();

    });
    function validateNumber(event) {
        var key = window.event ? event.keyCode : event.which;
        var keychar = String.fromCharCode(key);
        if (event.keyCode == 8 || event.keyCode == 46
            || event.keyCode == 37 || event.keyCode == 39) {
            return true;
        }
        else if ( key < 48 || key > 57 || keychar==".") {
            return false;
        }
        else return true;
    }
</script>
@endpush