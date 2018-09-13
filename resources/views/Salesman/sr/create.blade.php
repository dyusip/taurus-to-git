@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Salesman.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Salesman.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Sales Return</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Sales Return</strong>
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
                <form method="post" name="myform" action="/sr">
                    {{ csrf_field() }}
                    <input type="hidden" name="sr_code" id="sr_code" value="{{ $num }}">
                    <input type="hidden" name="sr_prepby" id="sr_prepby" value="{{ Auth::user()->username }}">
                    <input type="hidden" name="branch_code" id="branch_code" value="{{ Auth::user()->branch }}">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Sales Return Info <small>Fill in information available</small></h5>
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
                                            <div class="form-group"><label>SO #</label> <label id="company_code-error" class="error" for="so_code" style="display: none">This field is required.</label>
                                                <select class="form-control select2_demo_1" name="so_code" id="so_code">
                                                    <option value=""></option>
                                                    @foreach($sos as $so)
                                                        <option value="{{ $so->so_code }}">{{ $so->so_code." - ".$so->cust_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Customer Name</label> <input readonly type="text" style="text-transform: uppercase" id="cust_name" placeholder="Customer Name" class="form-control"></div>
                                            <div class="form-group"><label>Address</label> <input readonly type="text"  style="text-transform: uppercase" id="cust_address" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Contact #</label> <input readonly type="text" style="text-transform: uppercase" id="cust_contact" placeholder="Contact #" class="form-control"></div>

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
                                    <h5>Date and Mechanic</h5>
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
                                                <label> Return Delivered</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="reqDate" name="sr_date" class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>
                                            </div>
                                            <div class="form-group"><label>Sales Date</label> <input readonly type="text"  id="sales_date" placeholder="Sales Date" class="form-control"></div>
                                            <div class="form-group"><label>Salesman</label> <input readonly type="text" id="salesman" placeholder="Salesman" class="form-control"></div>
                                            <div class="form-group"><label>Mechanic</label> <input readonly type="text"  id="mechanic" placeholder="Mechanic" class="form-control"></div>
                                            <!--<div class="form-group"><label>Mechanic</label> <input type="text" placeholder="Mechanic" class="form-control"></div>
                                            <div class="form-group"><label>Address</label> <input type="text" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Amount Received</label> <input type="text" placeholder="Amount Received" class="form-control"></div>-->
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
                                    <h5>Order <small class="text-primary">List of item(s) to return <i class="fa fa-exclamation-circle"></i></small></h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="alert alert-danger" id="checker-alert" hidden></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="23%">Name</th>
                                                <th data-hide="phone" width="5%">UOM</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="8%">Price</th data-hide="phone,tablet" >
                                                <th data-hide="phone" width="6%">Discount</th>
                                                <th class="text-right" width="8%">Amount</th>
                                                <th class="text-center" width="8%">Remarks</th>
                                                <th class="text-center" width="4%">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="pull-right">
                                        <div class="form-inline">
                                            <label for="totalAmount">Total Amount</label>&nbsp;
                                            <input class="form-control text-right" readonly id="totalAmount" name="sr_total" type="text">
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <!--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>-->
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check-circle"></i> Return Order</button>
                                    </div>
                                    <!-- save-->
                                </div>
                            </div>
                        </div>
                    </div><!--row-->
                </form>
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
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script>
    $(document).ready(function () {
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
        $('[id^=qty]').keypress(validateNumber);
    });
    function Choosen() {
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
    }
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
    $(document).on('change','#so_code',function () {
        var so = $(this).val();
        if(so !="") {
            $.ajax({
                url: so,
                beforeSend: function () {
                    $('#main-spinner').fadeIn();
                },
                success: function (output) {
                    var data = jQuery.parseJSON(output);
                    $('#main-spinner').fadeOut();
                    $('tbody').html('');
                    $('#cust_name').val(data.header.cust_name);
                    $('#cust_address').val(data.header.cust_address);
                    $('#cust_contact').val(data.header.cust_contact);
                    $('#mechanic').val(data.mechanic);
                    $('#salesman').val(data.salesman);
                    $('#sales_date').val(data.header.so_date);
                    $('#totalAmount').val(data.header.total);
                    $.each(data.detail, function (index, value) {
                        var row = "<tr> " +
                            "<td><input type='text' name='prod_code[]' value='" + value.sod_prod_code + "' id='code' class='form-control code' readonly></td> " +
                            "<td><input type='text' name='prod_name[]' value='" + value.sod_prod_name + "' id='prod_name' class='form-control' readonly></td> " +
                            '<td><input type="text" name="uom[]" value="' + value.sod_prod_uom + '" id="uom" class="form-control" readonly></td> ' +
                            '<td><input type="hidden" id="order_qty" value="'+ value.sod_prod_qty +'"><input type="text" name="qty[]" value="' + value.sod_prod_qty + '" id="qty" class="form-control" required></td> ' +
                            '<td><input type="text" name="price[]" value="' + value.sod_prod_price + '" id="price" class="form-control" readonly></td> ' +
                            '<td><input type="text" name="less[]" value="' + value.sod_less + '" id="less" class="form-control" readonly></td> ' +
                            '<td><input type="text" name="amount[]" value="' + value.sod_prod_amount + '" id="amount" class="form-control amount" readonly></td> ' +
                            '<td class="text-center"><select name="status[]" id="status" class="form-control"><option value="SR">Sales Return</option><option value="BO">Bad Order</option></select></td> ' +
                            '<td class="text-center tooltip-demo"><a id="remove-row" class="text-danger" data-toggle="tooltip" title="Remove item"><i class="fa fa-remove"></i></a></td> ' +
                            '</tr>';
                        $('tbody').append(row);
                    });
                    $('[id^=qty]').keypress(validateNumber);
                    totalAmount();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + " " + thrownError);
                }
            });
        }

    });
    $('tbody').delegate('#qty','keyup',function () {
        var tr = $(this).parent().parent();
        var qty = $(this).val();
        var less = tr.find('#less').val();
        var price = tr.find('#price').val();
        var available = tr.find('#order_qty').val();
        if(Number(qty)>Number(available) || Number(qty) == 0){
            //alert('Quantity transfer should not exceeds available');
            var message = qty==0?'Return quantity should not be zero':'Return quantity should not exceeds quantity ordered';
            toastr.error(message);
            tr.find('#qty').val(available);
            totalAmount();
        }else{
            var amount = (price * qty - ((price * qty) * less/100)) ;
            tr.find('#amount').val(amount);
            totalAmount();
        }
    });
    $('body').delegate('#remove-row','click',function () {
        var tr = $('tbody tr').length;
        if(tr > 1){
            $(this).parent().parent().remove();
            $('.tooltip').hide();
            totalAmount();
        }else{
            toastr.error('You cannot remove the first field');
        }
    });
    function totalAmount() {
        var total = 0;//totalAmount
        $('.amount').each(function (i,e) {
            var amount = $(this).val()-0;
            total += amount;
        });
        $('#totalAmount').val(total)
    }
    $(function () {
        $('#datepicker').datepicker();
    });
    $('#reqDate').change(function () {
        ($(this).val() != "") ? $('#reqDate-error').hide() : $('#reqDate-error').show();
        ($(this).val() != "") ? $('#reqDate').removeClass('error') : $('#reqDate').addClass('error');
    });
    @if (session('status'))
        $(document).ready(function () {
        toastr.success("{{ session('status') }}");
    });
    @endif
</script>
@endpush