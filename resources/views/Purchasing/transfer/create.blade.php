@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Transfer Item</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{url('/home')}}">Home</a>
                        </li>
                        <li class="active">
                            <strong>Transfer Item</strong>
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
                <form method="post" name="myform" id="myform" action="/transfer">
                    {{csrf_field()}}
                    <input type="hidden" name="tf_code" id="tf_code" value="{{ $num }}">
                    <input type="hidden" name="from_branch" id="from_branch" value="{{ Auth::user()->branch }}">
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
                                                <select name="to_branch" id="branch_code" class="form-control select2_demo_1" required>
                                                    <option value=""></option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Address</label> <input readonly type="text" name="address" style="text-transform: uppercase" id="address" placeholder="Address" class="form-control"></div>
                                            <div class="form-group"><label>Contact #</label> <input readonly type="text" name="contact" style="text-transform: uppercase" id="contact" placeholder="Contact #" class="form-control"></div>
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
                                                    <input readonly placeholder="" type="text" id="reqDate" name="tf_date"
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
                                                <th data-hide="phone" width="5%">Available</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="7%">Cost</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center tooltip-demo" width="3%"><span data-toggle="tooltip" title="Add more item"><a href="#myModal" data-toggle="modal" ><i class="fa fa-plus-circle"></i></a></span></th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="22%">Name</th>
                                                <th data-hide="phone" width="6%">UOM</th>
                                                <th data-hide="phone" width="5%">Available</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="7%">Cost</th>
                                                <th class="text-center" width="8%">Amount</th>
                                                <th class="text-center tooltip-demo" width="3%"><span data-toggle="tooltip" title="Add more item"><a href="#myModal" data-toggle="modal" ><i class="fa fa-plus-circle"></i></a></span></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="btn-group ">
                                                {{--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>--}}
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check-circle"></i> Create PO</button>
                                            </div>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Amount</label>&nbsp;
                                                <input class="form-control text-right" readonly id="totalAmount" name="tf_amount" type="text">
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
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/chosen/chosen.jquery.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
{{--<script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>--}}

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
    $(document).on('change','#branch_code',function (e) {
        e.preventDefault();
        var branch = $(this).val();
        $.ajax({
            url: branch,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function (data) {
                $('#someSelect').html('');
                $('#main-spinner').fadeOut();
                $('#address').val(data.address);
                $('#contact').val(data.contact);
                $('tbody').html('');
                products();
                totalAmount();
                /*$.each(data.prod, function (index, value) {
                 $('#product').append("<option value='"+value.prod_code+"'>"+value.prod_code+"</option>");
                 });*/
            }
        });

    });

    $('tbody').delegate('#qty','keyup',function () {
        var tr = $(this).parent().parent();
        var qty = $(this).val();
        var available = tr.find('#available').val();
        var price = tr.find('#cost').val();
        if(qty>available){
            //alert('Quantity transfer should not exceeds available');
            toastr.error("Quantity transfer should not exceeds available");
            tr.find('#amount').val(0);
            tr.find('#qty').val('');
            totalAmount();
        }else{
            var amount = price * qty;
            tr.find('#amount').val(amount);
            totalAmount();
        }
    });
    $('#add-row').on('click',function () {
     //addRow();
     //Choosen();
     //Validate();
     products();
     });
    $('#add-count').on('click',function () {
        var tr = $('tbody tr').length;
        var count = $('#count').val();
        $('#main-spinner').fadeIn();
        if(tr < 101 && (parseInt(tr) + parseInt(count)) < 101){
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
        var branch = $('#branch_code').val();
        var count = $('#count').val();
        $.ajax({
            url: "create/"+branch,
            success: function (data) {
                //var data = jQuery.parseJSON(output);
                //$('.product').html('');
                for (var i = 0; i < count; i++) {
                    addRow();
                    Choosen();
                    Validate();
                    $("#order-tbl tr:last #product").append('<option value=""></option>');
                    $.each(data, function (index, value) {
                        $("#order-tbl tr:last #product").append("<option value='" + value.inventory.code + "'>" + value.inventory.name + "</option>");
                    });
                }
            }
        });
    }
    function addRow() {
        var row = "<tr> " +
            "<td>" +
            "<input type='text' name='prod_code[]' id='code' class='form-control code' readonly>" +
            "<input type='hidden' name='prod_name[]' id='prod_name' class='form-control' readonly> " +
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
        var branch = $('#branch_code').val();
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
        $.ajax({url: item+"/"+branch,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function(data) {
                //var data = jQuery.parseJSON(output);
                $('#item-error').fadeOut();
                var qty = tr.find('#qty').val();
                tr.find('#code').val(data.prod_code);
                tr.find('#prod_name').val(data.inventory.name);
                tr.find('#cost').val(data.cost);
                tr.find('#uom').val(data.inventory.uom);
                tr.find('#available').val(data.quantity);
                var amount = qty * data.cost;
                tr.find('#amount').val(amount);
                totalAmount();
                $('#main-spinner').fadeOut();

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError);
            }
        });
    });
    function totalAmount() {
        var total = 0;//totalAmount
        $('.amount').each(function (i,e) {
            var amount = $(this).val()-0;
            total += amount;
        });
        $('#totalAmount').val(total)
    }
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
</script>
<script type="text/javascript">
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