@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        <!-- Navigation -->
       @include('Salesman.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Salesman.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Create Order</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Sales Order</strong>
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
                <form method="post" name="myform" id="myform" action="/so">
                    {{ csrf_field() }}
                    <input type="hidden" name="branch_code" value="{{ Auth::user()->branch }}">
                    <input type="hidden" name="so_prepby" value="{{ Auth::user()->username }}">
                    <input type="hidden" name="so_code" value="{{ $num }}">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Customer Info <small>Fill in information available</small></h5>
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
                                            <div class="form-group"><label>Job Order #</label><input type="text" required name="jo_code" style="text-transform: uppercase" id="jo_code" placeholder="Job Order #" class="form-control"></div>
                                            <div class="form-group"><label>Customer Name</label> <input type="text" style="text-transform: uppercase" name="cust_name" id="cust_name" placeholder="Customer Name" class="form-control"></div>
                                            <div class="form-group"><label>Address</label> <input type="text" name="cust_address" style="text-transform: uppercase" id="cust_add" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Contact #</label> <input type="text" name="cust_contact" style="text-transform: uppercase" id="cust_contact" placeholder="Contact #" class="form-control"></div>
                                            <div class="form-group">
                                                <label>Salesman</label>
                                                <select id="so_salesman" required name="so_salesman" class="form-control">
                                                    <option value=""></option>
                                                    @foreach($salesmans as $salesman)
                                                        <option value="{{ $salesman->username }}">{{ $salesman->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!--<div>
                                                   <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Log in</strong></button>
                                                   <label> <input type="checkbox" class="i-checks"> Remember me </label>
                                               </div>-->

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
                                                <label> Date</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="reqDate" name="so_date"
                                                           class="form-control" required value="{{ date('m/d/Y') }}">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Mechanic</label>
                                                <select id="so_mechanic" name="so_mechanic" class="form-control">
                                                    <option value=""></option>
                                                    @foreach($mechanics as $mechanic)
                                                        <option value="{{ $mechanic->code }}">{{ $mechanic->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Service Charge</label> <input type="text" name="serv_charge" id="serv_charge" placeholder="Service Charge" class="form-control">
                                            </div>
                                            <div class="form-group"><label>Amount Received</label> <input type="text" name="amount_rec" id="amount_rec" placeholder="Amount Received" class="form-control">
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
                                    <h5>Order</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="10%">Code</th>
                                                <th data-hide="phone" width="23%">Name</th>
                                                <th data-hide="phone" width="5%">UOM</th>
                                                <th data-hide="phone" width="5%">Available</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="8%">Price</th>
                                                <th data-hide="phone,tablet" width="6%">Discount</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center tooltip-demo" width="5%"><a class="add-row" data-toggle="tooltip" title="Add more item"><i class="fa fa-plus-circle"></i></a></th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            <tr>
                                                <td>
                                                    <input type="text" required name="prod_code[]" id="code" class="form-control code" readonly>

                                                </td>
                                                <td><select class="form-control select2_demo_1" required tabindex="2" name="product" id="product">
                                                        <option></option>
                                                        @foreach($inventories as $inventory)
                                                            <option value="{{ $inventory->inventory->code }}"> {{ $inventory->inventory->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" required name="prod_name[]" id="prod_name" class="form-control" readonly>
                                                </td>
                                                <td><input type="text" name="uom[]" id="uom" class="form-control" readonly></td>
                                                <td><input type="text" id="available" readonly class="form-control qty" ></td>
                                                <td><input type="text" name="qty[]" id="qty" required class="form-control qty" ></td>
                                                <td><input type="text" name="price[]" id="price" class="form-control" required></td>
                                                <td><select name="less[]" id="less" class="form-control">
                                                        @for ($i = 0; $i < 31; $i++)
                                                            <option value="{{ $i }}">{{$i}}%</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                                <td><input type="text" name="amount[]" id="amount" class="form-control amount" readonly></td>
                                                <td class="text-center"><a class="text-danger"><i class="fa fa-remove"></i></a></td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th width="10%">Code</th>
                                                <th data-hide="phone" width="23%">Name</th>
                                                <th data-hide="phone" width="5%">UOM</th>
                                                <th data-hide="phone" width="5%">Available</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="8%">Price</th>
                                                <th data-hide="phone,tablet" width="6%">Discount</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center tooltip-demo" width="5%"><a class="add-row" data-toggle="tooltip" title="Add more item"><i class="fa fa-plus-circle"></i></a></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="btn-group ">
                                                <button  type="submit" class="btn btn-primary ladda-button ladda-button-demo btn-sm" data-style="zoom-in"><i class="fa fa-check-circle"></i> Save Order</button>
                                            </div>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Amount</label>&nbsp;
                                                <input class="form-control text-right" readonly id="totalAmount" name="so_amount" type="text">
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--row-->
                </form>
            </div>

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
<!--ladda-->
<script src="{{ asset('js/plugins/ladda/spin.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.min.js') }}"></script>
<script src="{{ asset('js/plugins/ladda/ladda.jquery.min.js') }}"></script>
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
    $(document).ready(function(){
        $('[id^=quantity]').keypress(validateNumber);
        $('[id^=quantity_edit]').keypress(validateNumber);
        Validate();
    });
    function Validate() {
        $('[id^=qty]').keypress(validateNumber);
        $('[id^=price]').keypress(validateNumber);
    }
    $('tbody').delegate('#product','change',function () {
        var tr = $(this).parent().parent();
        var item = $(this).val();
        $('#md-alert-error').hide();
        $('#md-alert-error').html('');
        $('.code').each(function (i,e) {
            if(item===$(this).val()){
                /*$('#item-error').html('Item already from the list(s)');
                $('#item-error').show();*/
                toastr.error("Item already from the list(s)");
                tr.find('input').val('');
                tr.find('#select2-product-container').html('');
                tr.find('#product').val('');
                totalAmount();
                throw "exit";
            }
        });

        $.ajax({url: item,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function(data) {
                //var data = jQuery.parseJSON(output);
                $('#item-error').fadeOut();
                var qty = tr.find('#qty').val();
                var less = tr.find('#less').val();
                tr.find('#code').val(data.prod_code);
                tr.find('#prod_name').val(data.inventory.name);
                //tr.find('#cost').val('₱'+parseFloat(data.cost).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                tr.find('#price').val(data.price);
                tr.find('#available').val(data.quantity);
                tr.find('#uom').val(data.inventory.uom);
                if(qty > data.quantity){
                    tr.find('#qty').val(0);
                    tr.find('#amount').val(0);
                    tr.find('#less').val(0);
                    totalAmount();
                }else{
                    var amount = (data.price * qty - ((data.price * qty) * less/100)) ;
                    tr.find('#amount').val(amount);
                    totalAmount();
                }
                $('#main-spinner').fadeOut();

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError);
            }
        });
    });
    $('tbody').delegate('#qty','keyup',function () {
        var tr = $(this).parent().parent();
        var qty = $(this).val();
        var less = tr.find('#less').val();
        var price = tr.find('#price').val();
        var available = tr.find('#available').val();
        if(Number(qty)>Number(available)){
            //alert('Quantity transfer should not exceeds available');
            toastr.error("Quantity order should not exceeds available");
            tr.find('#amount').val(0);
            tr.find('#qty').val('');
            totalAmount();
        }else{
            var amount = (price * qty - ((price * qty) * less/100)) ;
            tr.find('#amount').val(amount);
            totalAmount();
        }
    });

    $('tbody').delegate('#price','keyup',function () {
        var tr = $(this).parent().parent();
        var price = $(this).val();
        var less = tr.find('#less').val();
        var qty = tr.find('#qty').val();
        var available = tr.find('#available').val();
        if(Number(qty)>Number(available)){
            //alert('Quantity transfer should not exceeds available');
            toastr.error("Quantity order should not exceeds available");
            tr.find('#amount').val(0);
            tr.find('#qty').val('');
            totalAmount();
        }else{
            var amount = (price * qty - ((price * qty) * less/100)) ;
            tr.find('#amount').val(amount);
            totalAmount();
        }
    });
    $('tbody').delegate('#less','change',function () {
        var tr = $(this).parent().parent();
        var less = $(this).val();
        var price = tr.find('#price').val();
        var qty = tr.find('#qty').val();
        var amount = (price * qty - ((price * qty) * less/100)) ;
        tr.find('#amount').val(amount);
        totalAmount();
    });
    function totalAmount() {
        var total = 0;//totalAmount
        $('.amount').each(function (i,e) {
            var amount = $(this).val()-0;
            total += amount;
        });
        $('#totalAmount').val(total)
    }
    $('.add-row').on('click',function () {
        addRow();
        Choosen();
        Validate();
    });
    function addRow() {
        var row = "<tr> " +
            "<td>" +
            "<input type='text' name='prod_code[]' id='code' class='form-control code' readonly>" +
            "<input type='hidden' name='prod_name[]' id='prod_name' class='form-control' readonly> " +
            "</td> " +
            "<td><select class='form-control select2_demo_1' required tabindex='2' name='product' id='product'> " +
            "<option></option>"+
            '@foreach($inventories as $inventory)'+
            '<option value="{{ $inventory->inventory->code }}"> {{ $inventory->inventory->name }}</option>'+
            '@endforeach' +
            '</select></td> ' +
            '<td><input type="text" name="uom[]" id="uom" class="form-control" readonly></td> ' +
            '<td><input type="text" id="available" readonly class="form-control qty" ></td> ' +
            '<td><input type="text" name="qty[]" required id="qty" class="form-control qty" ></td> ' +
            '<td><input type="text" name="price[]" id="price" class="form-control" required></td> ' +
            '<td><select name="less[]" id="less" class="form-control">'+
                '@for ($i = 0; $i < 31; $i++)'+
            '<option value="{{ $i }}">{{$i}}%</option>'+
                '@endfor'+
            '</select></td> ' +
            '<td><input type="text" name="amount[]" id="amount" class="form-control amount" readonly></td> ' +
            '<td class="text-center tooltip-demo"><a id="remove-row" class="text-danger" data-toggle="tooltip" title="Remove item"><i class="fa fa-remove"></i></a></td> ' +
            '</tr>';
        $('tbody').append(row);
    }
    $('body').delegate('#remove-row','click',function () {
        $(this).parent().parent().remove();
        $('.tooltip').hide();
        totalAmount();
    });
    $(document).on('submit','#myform',function () {
        var l = $( '.ladda-button-demo' ).ladda();
        l.ladda( 'start' );
    });
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