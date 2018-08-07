@extends('layout.main')

@section('index-content')
<div id="wrapper">

    <!-- Navigation -->
    @include('Purchasing.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('Purchasing.header')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Receving</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="/index">Home</a>
                    </li>
                    <li class="active">
                        <strong>Receiving</strong>
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
            <form method="post" name="myform" action="/receiving">
                {{ csrf_field() }}
                <input type="hidden" class="form-control" name="rh_no" id="rh_no" value="{{ $num }}">
                <input type="hidden" class="form-control" name="rh_branch_code" id="rh_no" value="{{ Auth::user()->branch }}">
                <input type="hidden" class="form-control" name="rh_prepby" id="rh_no" value="{{ Auth::user()->username }}">
                <input type="hidden" class="form-control" name="rh_status" id="status">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Receiving Info <small>Fill in information available</small></h5>
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
                                        <div class="form-group"><label>PO #</label> <label id="company_code-error" class="error" for="DR_No" style="display: none">This field is required.</label>
                                            <select required class="form-control select2_demo_1" name="rh_po_no" id="PO_No">
                                                <option value=""></option>
                                                @foreach($pos as $po)
                                                    <option value="{{ $po->po_code }}">{{ $po->po_code ." - ". $po->sup_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group"><label>SI #</label> <label id="company_code-error" class="error" for="DR_No" style="display: none">This field is required.</label>
                                            <input required type="text" class="form-control" placeholder="Sales Invoice #" name="rh_si_no" id="si_no">
                                        </div>
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
                    <div class="col-lg-5">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Date</h5>
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
                                            <label> Date Delivered</label>
                                            <label id="reqDate-error" class="error" for="reqDate" style="display: none">Required.</label>
                                            <div class='col-md-12 col-sm-12 col-xs-12 input-group date' id='datepicker'>
                                                <input readonly placeholder="" type="text" id="reqDate" name="rh_date" class="form-control" required value="<?php echo date('m/d/Y');?>">
                                                <span class="input-group-addon"><a><span class="fa fa-calendar"></span></a></span>
                                            </div>
                                            <script type="text/javascript">
                                                $(function () {
                                                    $('#datepicker').datepicker();
                                                });
                                                $('#reqDate').change(function() {
                                                    ($(this).val() !="")?$('#reqDate-error').hide():$('#reqDate-error').show();
                                                    ($(this).val() !="")?$('#reqDate').removeClass('error'):$('#reqDate').addClass('error');
                                                });
                                            </script>
                                        </div>
                                        <div class="form-group"><label>Supplier's Contact #</label> <input type="text" id="sup_contact" placeholder="Contact #" class="form-control" readonly></div>
                                        <!--<div class="form-group"><label>Mechanic</label> <input type="text" placeholder="Mechanic" class="form-control"></div>
                                        <div class="form-group"><label>Address</label> <input type="text" placeholder="Address" class="form-control"></div>
                                        <div class="form-group"><label>Amount Received</label> <input type="text" placeholder="Amount Received" class="form-control"></div>-->
                                    </div>
                                    <div class="col-sm-5"><h4>Not a member?</h4>
                                        <p>You can create an account:</p>
                                        <p class="text-center">
                                            <!--<i class="fa fa-wrench big-icon"></i>-->
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
                                <h5>Order <small class="text-danger">Note: Please check all item(s) before hitting receive <i class="fa fa-exclamation-circle"></i></small></h5>
                            </div>
                            <div class="ibox-content">
                                <div class="alert alert-danger" id="checker-alert" hidden></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-salesOrder" data-page-size="15">
                                        <thead>
                                        <tr>
                                            <th width="9%">Code</th>
                                            <th data-hide="phone" width="20%">Name</th>
                                            <th data-hide="phone" width="5%">UOM</th>
                                            <th data-hide="phone" width="6%">Qty Purchase</th>
                                            <th data-hide="phone" width="6%">Qty Receive</th>
                                            <th data-hide="phone" width="5%">Status</th>
                                            <th class="text-center" width="4%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="order-tbl" class="tooltip-demo">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-right">
                                    <div class="form-inline">
                                        <label for="totalAmount">PO Amount</label>&nbsp;
                                        <input class="form-control text-right" readonly id="totalAmount" name="overall_total" type="text">
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check-circle"></i> Receive</button>
                                </div>
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
    $(document).on('change','#PO_No',function (e) {
        e.preventDefault();
        var po = $(this).val();

        $.ajax({url: po,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function(output) {
                var data = jQuery.parseJSON(output);
                $('#main-spinner').fadeOut();
                $('tbody').html('');
                $('#sup_contact').val(data.header.sup_contact);
                $('#totalAmount').val(data.header.total);
                $.each(data.detail, function (index, value) {
                    var row = "<tr> " +
                        "<td><input type='text' name='prod_code[]' value='"+value.prod_code+"' id='code' class='form-control code' readonly></td> " +
                        "<td><input type='text' name='prod_name[]' value='"+value.prod_name+"' id='prod_name' class='form-control' readonly></td> " +
                        '<td><input type="text" name="uom[]" value="'+value.prod_uom+'" id="uom" class="form-control" readonly></td> ' +
                        '<td><input type="text" name="qty[]" value="'+value.prod_qty+'" id="qty" class="form-control" readonly></td> ' +
                        '<td><input type="text" name="rec_qty[]" required id="rec_qty" class="form-control"></td> ' +
                        '<td class="text-center"><input type="text" name="status[]" id="status" class="form-control status" readonly></td> ' +
                        '<td class="text-center tooltip-demo"><a id="remove-row" class="text-danger" data-toggle="tooltip" title="Remove item"><i class="fa fa-remove"></i></a></td> ' +
                        '</tr>';
                    $('tbody').append(row);
                });
                checkStatus();

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError);
            }
        });
    });
    //put received qty
    $('tbody').delegate('#rec_qty','keyup',function () {
        var tr = $(this).parent().parent();
        var rec = $(this).val();
        var qty = tr.find('#qty').val();
        if(rec > qty){
            tr.find('#status').val('EX');
        }else if(rec < qty){
            tr.find('#status').val('LA');
        }else if(rec == qty){
            tr.find('#status').val('RE');
        }
        checkStatus();
    });
    //remove
    $('body').delegate('#remove-row','click',function () {
        $(this).parent().parent().remove();
        $('.tooltip').hide();
        checkStatus();
    });
    //check Status
    function checkStatus() {
        var remarks = 'CL';//totalAmount
        $('.status').each(function (i,e) {
            if($(this).val()!=='RE' && $(this).val()!=='EX'){
                remarks = 'OP'
            }
        });
        $('#status').val(remarks)
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