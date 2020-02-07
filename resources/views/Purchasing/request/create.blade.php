@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Pick List Request</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{url('/home')}}">Home</a>
                        </li>
                        <li class="active">
                            <strong>Pick List Request</strong>
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
                <form method="post" name="myform" id="myform" action="/replenish/store">
                    {{csrf_field()}}
                   {{-- <input type="hidden" name="tf_code" id="tf_code" value="{{ $num }}">--}}
                    <input type="hidden" name="req_from" id="req_from" value="{{ $branch->code }}">
                    <input type="hidden" name="req_to" id="req_to" value="TR-BR00001">
                    <input type="hidden" name="req_reqby" id="req_reqby" value="{{ Auth::user()->username }}">
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
                                            <div class="form-group"><label>Branch Name</label> <input readonly type="text" value="{{ $branch->name }}" name="name" style="text-transform: uppercase" id="name" placeholder="Name" class="form-control"></div>
                                            <div class="form-group"><label>Address</label> <input readonly type="text" value="{{ $branch->address }}" name="address" style="text-transform: uppercase" id="address" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Contact #</label> <input readonly type="text" value="{{ $branch->contact }}" name="contact" style="text-transform: uppercase" id="contact" placeholder="Contact #" class="form-control"></div>
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
                                                <label> Date</label>
                                                <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                                <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                    <input readonly placeholder="" type="text" id="reqDate" name="req_date"
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
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="22%">Name</th>
                                                <th data-hide="phone" width="6%">UOM</th>
                                                <th data-hide="phone" width="5%">CW Stocks</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="7%">Cost</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                {{--<th class="text-center" data-hide="phone,tablet" width="3%">#</th>--}}
                                                <th class="text-center tooltip-demo" width="3%"><span data-toggle="tooltip" title="Add more item"><a href="#myModal" data-toggle="modal" ><i class="fa fa-plus-circle"></i></a></span></th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            @php
                                                $total = 0;
                                                $count = 0;
                                            @endphp
                                            @foreach($inventories as $inventory)
                                            @php $count++; @endphp
                                                <tr>
                                                    <td><input type="text" name="prod_code[]" id="prod_code" value="{{ $inventory->sod_prod_code }}" class="form-control code" readonly></td>
                                                    <td><input type="text" name="prod_name[]" id="prod_name" value="{{ $inventory->sod_prod_name }}" class="form-control" readonly></td>
                                                    <td><input type="text" name="uom[]" id="uom" value="{{ $inventory->sod_prod_uom }}" class="form-control" readonly></td>
                                                    <td><input type="text" id="available" value="{{ $inventory->cw_qty }}" class="form-control" readonly></td>
                                                    <td><input type="text" name="qty[]" id="qty" value="{{ $inventory->qty }}" class="form-control qty"></td>
                                                    <td><input type="text" name="cost[]" id="cost" value="{{ $inventory->cost }}" class="form-control" readonly>
                                                        <input type="hidden" name="prod_cost[]" id="prod_cost" value="{{  $inventory->cost }}" class="form-control" readonly>
                                                        <input type="hidden" name="prod_srp[]" id="prod_srp" value="{{ $inventory->price }}" class="form-control" readonly>
                                                    </td>
                                                    <td><input type="text" name="amount[]" id="amount" value="{{ $inventory->total  }}" class="form-control amount" readonly></td>
                                                    <td class='text-center tooltip-demo'><a id='remove-row' class='text-danger' data-toggle='tooltip' title='Remove item'><i class='fa fa-remove'></i></a></td>
                                                    @php $total +=  $inventory->total @endphp
                                                </tr>
                                            @endforeach
                                            <input type="hidden" id="picklist_count" value="{{$count}}">
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="22%">Name</th>
                                                <th data-hide="phone" width="6%">UOM</th>
                                                <th data-hide="phone" width="5%">CW Stocks</th>
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

                                            <div class="btn-group ">
                                                {{--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>--}}
                                                <button type="submit" class="btn btn-primary ladda-button ladda-button-demo btn-sm" data-style="zoom-in"><i class="fa fa-check-circle"></i> Create Request</button>
                                            </div>
                                            <div class="btn-group ">
                                                {{--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>--}}
                                                <button type="submit" form="form_analysis" class="btn btn-success ladda-button ladda-button-demo btn-sm" data-style="zoom-in"><i class="fa fa-mail-reply"></i> Back to Inventory Analysis</button>
                                            </div>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Amount</label>&nbsp;
                                                <input class="form-control text-right" readonly id="totalAmount" value="{{ $total }}" name="req_amount" type="text">
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--row-->
                </form>
                <form action="/inventory_analysis" method="post" id="form_analysis">
                    {{csrf_field()}}
                    <input type="hidden" name="branch" value="{{ @$request->branch_code }}">
                    <input type="hidden" name="start" value="{{ @$request->from }}">
                    <input type="hidden" name="end" value="{{ @$request->to }}">
                    <input type="hidden" name="optCustType" value="branch">
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
    }
    function Choosen() {
        //(".select2_demo_1").select2();
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
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
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
    });
    $('tbody').delegate('#qty','keyup',function () {
        var tr = $(this).parent().parent();
        var qty = $(this).val();
        var available = tr.find('#available').val();
        var price = tr.find('#cost').val();
        if(Number(qty)>Number(available)){
            //alert('Quantity transfer should not exceeds available');
            toastr.error("Quantity request should not exceeds available");
            tr.find('#amount').val(0);
            tr.find('#qty').val('');
            totalAmount();
        }else{
            var amount = price * qty;
            tr.find('#amount').val(amount.toFixed(2));
            totalAmount();
        }
    });
    //add item
    $('#add-count').on('click',function () {
        var tr = $('tbody tr').length;
        var count = $('#count').val();
        $('#main-spinner').fadeIn();
        var picklist = Number($('#picklist_count').val()) + 31;
        if(tr < picklist && (parseInt(tr) + parseInt(count)) < picklist){
            products();
            $('#main-spinner').fadeOut();
            $('#myModal').modal('hide');
            count = $('#count').val('');
        }else{
            $('#main-spinner').fadeOut();
            toastr.error("Item exceeds the limit.");
        }
    });
    function products() {
        var branch = $('#req_from').val();
        var count = $('#count').val();
        $.ajax({
            url: "replenish/"+branch,
            success: function (data) {
                //var data = jQuery.parseJSON(output);
                //$('.product').html('');
                for (var i = 0; i < count; i++) {
                    addRow();
                    Choosen();
                    Validate();
                    var output = [];
                    $("#order-tbl tr:last #product").append('<option value=""></option>');
                    $.each(data, function (index, value) {
                        //items.push({ id: value.inventory.code, text : value.inventory.name});
                        //$("#order-tbl tr:last #product").append("<option value='" + value.inventory.code + "'>" + value.inventory.name + "</option>");
                        output.push("<option value='" + value.inventory.code + "'>" + value.inventory.name + "</option>");
                    });
                    $("#order-tbl tr:last #product").append(output.join(''));
                }
            }
        });
    }
    function addRow() {
        var row = "<tr> " +
            "<td>" +
            "<input type='text' name='prod_code[]' id='code' class='form-control code' readonly>" +
            "<input type='hidden' name='prod_name[]' id='prod_name' class='form-control' readonly> " +
            "<input type='hidden' name='prod_cost[]' id='prod_cost' class='form-control' readonly> " +
            "<input type='hidden' name='prod_srp[]' id='prod_srp' class='form-control' readonly> " +
            "</td> " +
            "<td><select class='form-control select2_demo_1 product' required tabindex='2' name='product' id='product'> " +
            '</select></td> ' +
            '<td><input type="text" name="uom[]" id="uom" class="form-control" readonly></td> ' +
            '<td><input type="text"  readonly id="available" class="form-control qty" ></td> ' +
            '<td><input type="text" name="qty[]" required id="qty" class="form-control qty" ></td> ' +
            '<td><input type="text" name="cost[]" id="cost" class="form-control" readonly></td> ' +
            '<td><input type="text" name="amount[]" id="amount" class="form-control amount" readonly></td> ' +
            '<td class="text-center tooltip-demo"><a id="remove-row" class="text-danger" data-toggle="tooltip" title="Remove item"><i class="fa fa-remove"></i></a></td> ' +
            '</tr>';
        $('tbody').append(row);
    }
    $('tbody').delegate('#product','change',function () {
        var tr = $(this).parent().parent();
        var item = $(this).val();
        var branch = $('#req_from').val();
        $('#md-alert-error').hide();
        $('#md-alert-error').html('');
        $('.code').each(function (i,e) {
            if(item===$(this).val()){
                toastr.error("Item already from the list(s)");
                tr.find('input').val('');
                tr.find('#select2-product-container').html('');
                tr.find('#product').val('');
                throw "exit";
            }
        });
        $.ajax({url: "replenish/"+item+"/"+branch,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function(data) {
                //var data = jQuery.parseJSON(output);
                $('#item-error').fadeOut();
                if(Number(tr.find('#qty').val()) > Number(data.quantity)){
                    tr.find('#amount').val(0);
                    tr.find('#qty').val('');
                }else{
                    var qty = tr.find('#qty').val();
                    var amount = qty * data.cost;
                    tr.find('#amount').val(amount);
                }
                tr.find('#code').val(data.prod_code);
                tr.find('#prod_name').val(data.inventory.name);
                tr.find('#cost').val(data.cost);
                tr.find('#uom').val(data.inventory.uom);
                tr.find('#available').val(data.quantity);
                tr.find('#prod_cost').val(data.cost);
                tr.find('#prod_srp').val(data.price);

                totalAmount();
                $('#main-spinner').fadeOut();

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError);
            }
        });
    });
    //end of add item
    function totalAmount() {
        var total = 0;//totalAmount
        $('.amount').each(function (i,e) {
            var amount = $(this).val()-0;
            total += amount;
        });
        $('#totalAmount').val(total.toFixed(2))
    }
    $(document).ready(function () {
        $('.qty').each(function (i,e) {
            var tr = $(this).parent().parent();
            var available = tr.find('#available').val();
            var qty = tr.find(this).val();
            if(Number(qty) > Number(available) && Number(available) != 0){
                tr.find(this).val(available);
            }else if(Number(available) == 0){
                $(this).parent().parent().remove();
            }
        });
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
    $(document).ready(function(){
        $('[id^=qty]').keypress(validateNumber);
    });
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
    /*4:00 AM - Pickup to your hotel
    7:00 AM - Breakfast in Oslob
    7:30 AM - Whale Shark swimming
    11:00 AM - Cooldown at Tumalog Falls
    11:30 AM - Lunch
    1:30 - Wash-up
    2:00 - Travel Back to Cebu City
    3:00 - Drop off in your hotel in Cebu or Mactan*/

</script>
@endpush