@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>View Purchase Request</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{url('/home')}}">Home</a>
                        </li>
                        <li class="active">
                            <strong>View Purchase Request</strong>
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
                <form method="post" name="myform" id="myform" action="/pr_view">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Purchase Request Info <small>Fill in information available</small></h5>
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
                                            <div class="form-group"><label>PR #</label>
                                                {{--<input type="text" required style="text-transform: uppercase" value="{{ @$po->sup_name }}" readonly placeholder="Supplier Name" class="form-control">--}}
                                                <select required name="PONo" id="po_code" class="form-control select2_demo_1">
                                                    <option></option>
                                                    @foreach($prs as $key)
                                                        <option value="{{ $key->prh_no }}">{{ $key->prh_no }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Requested by</label> <input type="text" value="" id="req_by" readonly style="text-transform: uppercase" placeholder="Requestor" class="form-control"></div>
                                            <div class="form-group"><label>Approved by</label> <input type="text" value="" id="app_by" readonly style="text-transform: uppercase" placeholder="Approver" class="form-control"></div>

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
                                    <h5>Date and PO#</h5>
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
                                                           class="form-control" required value="{{ @$po->po_date }}">
                                                    <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label>Term</label>
                                                <input readonly type="text" id="term" class="form-control" value="{{ @$po->term }}">
                                            </div>
                                            {{--<div class="form-group">
                                                <label>PO#</label>
                                                <input required onkeypress="return false;" onkeydown="return false;" type="text" name="PONo" class="form-control" value="{{ @$po->po_code }}">
                                            </div>--}}
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
                                        <button data-toggle="tooltip" title="Print Request" id="btn-print" class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/pr_view">
                                            <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                        </button>
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-example" data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="9%">Code</th>
                                                <th data-hide="phone" width="23%">Name</th>
                                                <th data-hide="phone" width="5%">UOM</th>
                                                <th data-hide="phone" width="6%">Qty</th>
                                                <th data-hide="phone,tablet" width="8%">Price</th>
                                                <th class="text-center" width="8%">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            {{--@if(isset($po))
                                            @foreach($po->po_detail as $product)
                                                <tr>
                                                    <td>{{ $product->prod_code }}</td>
                                                    <td>{{ $product->prod_name }}</td>
                                                    <td>{{ $product->prod_uom }}</td>
                                                    <td>{{ $product->prod_qty }}</td>
                                                    <td>{{ $product->prod_price }}</td>
                                                    <td>{{ $product->prod_amount }}</td>
                                                </tr>
                                            @endforeach
                                                @endif--}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="btn-group ">
                                                {{--<a href="#add-prod-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Add Item</a>--}}
                                                <button type="submit" name="status" value="AP" class="btn btn-primary btn-sm"><i class="fa fa-thumbs-up"></i> Approve PO</button>
                                                <button type="submit" name="status" value="NA" class="btn btn-warning btn-sm"><i class="fa fa-thumbs-down"></i> Disapprove PO</button>
                                            </div>

                                        </div>
                                        <div class="col-md-6 text-right">

                                            <div class="form-inline">
                                                <label for="totalAmount">Total Amount</label>&nbsp;
                                                <input class="form-control text-right" readonly id="totalAmount" name="pr_total" type="text" value="{{ @$po->total }}">
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
<link href="{{ asset('/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
{{--<script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>--}}
<script type="text/javascript">
    @if (session('status'))
        $(document).ready(function () {
        toastr.success("{{ session('status') }}");
    });
    @endif
    $(document).ready(function() {
        //datatable();
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
    function datatable() {
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
    }
    $(document).ready(function () {
        Choosen();
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
    $(document).on('change', '#po_code', function (e) {
        e.preventDefault();
        var po_code = $(this).val();
        $('#main-spinner').fadeIn();
        if(po_code == ""){
            $("input[type=text]").val('');
            /*$('#dataTables-example').DataTable().clear().destroy();
             datatable();*/
            $('#main-spinner').fadeOut();
        }else{
            $.ajax({
                url: "pr_view/"+po_code,
                beforeSend: function() {
                    $('#main-spinner').fadeIn();
                },
                success: function (output) {
                    var data = jQuery.parseJSON(output);
                    $('#req_by').val(data.requested.name);
                    $('#app_by').val(data.approved.name);
                    $('#reqDate').val(data.header.pr_date);
                    //$('#term').val(data.header.term);
                    $('#totalAmount').val(data.header.pr_total.toFixed(2));
                    $('#main-spinner').fadeOut();
                    $('tbody').html('');
                    /* if ($.fn.dataTable.isDataTable('#dataTables-example')) {
                     $('#dataTables-example').DataTable().clear().destroy();
                     }*/
                    $.each(data.detail, function (index, value) {
                        $('tbody').append("<tr> " +
                            "<td>"+ value.prd_prod_code +"</td> " +
                            "<td>"+ value.prd_prod_name +"</td> " +
                            "<td>"+ value.prd_prod_uom +"</td> " +
                            "<td>"+ value.prd_prod_qty +"</td> " +
                            "<td>"+ value.prd_prod_price.toFixed(2) +"</td> " +
                            "<td>"+ value.prd_prod_amount.toFixed(2) +"</td> " +
                            "</tr>");
                    });
                    // datatable();

                }
            });
        }
    });
    $('tbody').delegate('#qty','keyup',function () {
        var tr = $(this).parent().parent();
        var qty = $(this).val();
        var price = tr.find('#cost').val();
        var amount = price * qty;
        tr.find('#amount').val(amount.toFixed(2));
        totalAmount();
    });
    function totalAmount() {
        var total = 0;//totalAmount
        $('.amount').each(function (i,e) {
            var amount = $(this).val()-0;
            total += amount;
        });
        $('#totalAmount').val(total.toFixed(2))
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
@endpush