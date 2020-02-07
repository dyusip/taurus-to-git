@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Inventory Analysis Report</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>
                            <a>Taurus</a>
                        </li>
                        <li class="active">
                            <strong>Inventory Analysis Report</strong>
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
                                <h5>View</h5>
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
                            <form role="form" action="/inventory_analysis" method="post" name="myform" id="myform">
                                <div class="ibox-content">
                                    {{ csrf_field() }}
                                    <p>Show report by</p>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-md-1"><input  id="rd1" required name="optCustType" type="radio"  value="all" {{ (@$request->optCustType == 'all' ? 'checked':"") }}>
                                                    <label for="rd1" > All </label></div>
                                                <div class="col-md-2" style="margin-right: -95px;"><input id="rd2" name="optCustType" type="radio" value="branch" {{ (@$request->optCustType == 'branch' ? 'checked':"") }}>
                                                    <label for="rd2" > Branch </label></div>
                                                <div class="col-md-4">
                                                    <select data-placeholder="Choose a Branch..." class="select2_demo_1 form-control"  tabindex="2" name="branch" id = "cust_name" required {{ (@$request->optCustType == 'all' || @$request->optCustType == "") ? 'disabled':'' }}>
                                                        <option value="">Select Branch</option>
                                                        @foreach($branches as $branch)
                                                            @php
                                                            if(@$request->branch == $branch->code)
                                                            {
                                                                $chosen_branch = $branch->name;
                                                            }
                                                            @endphp
                                                            <option value="{{ $branch->code }}" {{ (@$request->branch == $branch->code)?'selected':'' }}>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5" >
                                                    {{--<div class="form-in">
                                                        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                            <span></span> <b class="caret"></b>
                                                            <input  type='hidden' name='from' id="from" value=''/>
                                                            <input  type='hidden' name='to' id="to" value=''/>
                                                        </div>
                                                    </div>--}}
                                                    <div class="input-group" id="data_5">
                                                        <div class="input-daterange input-group" id="datepicker">
                                                            <input type="text" class="input-sm form-control" name="start" value="{{ (isset($request->start))?$request->start:date('m/d/Y') }}"/>
                                                            <span class="input-group-addon">to</span>
                                                            <input type="text" class="input-sm form-control" name="end" value="{{ (isset($request->end))?$request->end:date('m/d/Y') }}" />
                                                        </div>
                                                        <span class="input-group-btn">
                                        <button type="submit" id="btn-search" class="btn btn-sm btn-primary"> Go!</button> </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    {{--<hr>
                                    <div class="row">
                                        <div class="col-md-4 col-md-offset-4">
                                            <div class="form-in">
                                                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                                    <span></span> <b class="caret"></b>
                                                    <input  type='hidden' name='from' id="from" value=''/>
                                                    <input  type='hidden' name='to' id="to" value=''/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" id="btn-search" name="btn-search" class="btn btn-sm btn-primary"> Go!</button>
                                        </div>
                                    </div>--}}
                                </div>
                                <div class="ibox-content tooltip-demo">
                                    <button data-toggle="tooltip" title="Print"  class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/inventory_analysis/print">
                                        <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                    </button>
                                    <div class="pull-right">
                                    <h3 class="text-danger">Legend*</h3>
                                    <div class="checkbox checkbox-primary checkbox-inline">
                                        <input onclick="return false;" type="checkbox" id="inlineCheckbox1" value="option1" checked>
                                        <label for="inlineCheckbox1"> Pick List </label>
                                    </div>
                                    <div class="checkbox checkbox-success checkbox-inline">
                                        <input onclick="return false;" type="checkbox" id="inlineCheckbox2" value="option1" checked="">
                                        <label for="inlineCheckbox2"> Purchase Request </label>
                                    </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="tbl-salesreport"
                                               data-page-size="15">
                                            <thead>
                                            <tr>
                                                {{--<th data-hide="phone" width="9%">BRANCH</th>--}}
                                                <th data-hide="phone" width="7%">ITEM CODE</th>
                                                <th data-hide="phone" width="23%">NAME</th>
                                                <th data-hide="phone" width="9%">UOM</th>
                                                <th data-hide="phone" width="5%">BRS</th>
                                                <th data-hide="phone" width="5%">CW</th>
                                                <th data-hide="phone,tablet" width="5%">{!! isset($request->branch)?$chosen_branch:'STOCKS' !!}</th>
                                                <th data-hide="phone,tablet" width="5%">SOLD</th>
                                                <th class="text-center" data-hide="phone,tablet" width="7%">#</th>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            @if(isset($sales))
                                                @php $total = 0; @endphp
                                                @foreach($sales as $sale)
                                                    {{--@foreach($sale->so_detail as $story)--}}
                                                    {{--@foreach( $inventories as $inventory)
                                                        @if($inventory->prod_code == $sale->sod_prod_code)
                                                            @php $onhand = $inventory->totalqty  @endphp
                                                        @endif
                                                    @endforeach--}}
                                                    <tr>
                                                        {{--<td class="text-center">{{ ucfirst($request->optCustType) }}</td>--}}
                                                        <td>{{ $sale->sod_prod_code }}</td>
                                                        <td>{{ $sale->sod_prod_name }}</td>
                                                        <td>{{ $sale->sod_prod_uom }}</td>
                                                        <td><a href="#md-modal" id="view-qty" data-prod="{{ $sale->sod_prod_code }}" data-toggle="modal">{{ $sale->brs_qty }}</a></td>
                                                        <td>{{ $sale->cw_qty }}</td>
                                                        <td>{{ $sale->totalqty }}</td>
                                                        <td>{{ $sale->qty}}</td>
                                                        <td class="text-center">
                                                            <div class="checkbox checkbox-primary checkbox-inline">
                                                                <input type="checkbox" {!! ($sale->cw_qty <= 0)?'disabled':'' !!} id="checkbox" class="checkbox" value="{{ $sale->sod_prod_code }}">
                                                                <label for="checkbox"></label>
                                                            </div>
                                                            <div class="checkbox checkbox-success checkbox-inline" style="margin-top: -3px;padding-left: 0px;">
                                                                <input type="checkbox" id="checkbox1" class="checkbox1" value="{{ $sale->sod_prod_code }}">
                                                                <label for="checkbox1">
                                                                </label>
                                                            </div>
                                                            <input type="hidden" id="quantity" value="{{ $sale->qty }}">
                                                        </td>
                                                    </tr>

                                                    {{-- @endforeach--}}
                                                @endforeach
                                            @endif
                                            </tbody>
                                            {{-- <tr>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <td style="display: none;"></td>
                                                 <th class="text-right" colspan="6">TOTAL AMOUNT</th>
                                                 <th>{{ @$total }}</th>
                                             </tr>--}}

                                        </table>

                                        <div class="mail-tools tooltip-demo m-t-md">

                                                <div class="pull-right">
                                                    <button type="submit" form="replenish" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="left" title="" data-original-title="Create Purchase Request"><i class="fa fa-mail-forward"></i> Request Item</button>
                                                </div>

                                            {{--<button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Mark as read"><i class="fa fa-eye"></i> </button>
                                            <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Mark as important"><i class="fa fa-exclamation"></i> </button>
                                            <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to trash"><i class="fa fa-trash-o"></i> </button>--}}

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form action="/replenish" id="replenish" method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="branch_code" value="{{ @$request->branch }}">
                                <input type="hidden" name="from" value="{{ @$request->start }}">
                                <input type="hidden" name="to" value="{{ @$request->end }}">
                            </form>
                            <div class="modal inmodal" id="md-modal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog animated flipInY">
                                    <div class="modal-content animated bounceInRight">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <i class="fa fa-gears modal-icon"></i>
                                            <h4 class="modal-title" id="md-title">Stocks</h4>
                                            <small class="font-bold">Available stocks in the following branches.</small>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-hover no-margins">
                                                <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th>Quantity</th>

                                                </tr>
                                                </thead>
                                                <tbody id="ul-itm">

                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                            {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                                        </div>
                                    </div>
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
                    <strong>Copyright</strong> INFOZ-ITWORKS &copy; 2017
                </div>
            </div>

        </div>
    </div>
@endsection
@push('styles')
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('css/plugins/datapicker/datepicker3.css' )}}" rel="stylesheet">
<link href="{{ asset('/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">


@endpush
@push('scripts')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- Data picker -->
<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!-- Date range picker -->
<script src="{{ asset('js/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script>
    $(document).ready(function () {
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
        $('input[name="daterange"]').daterangepicker();
        $('#data_5 .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });

        $('#tbl-salesreport').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "bSort" : false,
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
        /*$('input[type="checkbox"]').on('click', function() {
            alert();

        });*/
        $("tbody").delegate("#checkbox", "change", function(){
            var tr = $(this).parent().parent();
            var qty = tr.find('#quantity').val();
            if($(this).prop('checked')){
                //btn.removeAttr('disabled');
                $('#replenish').append('<div class="new_item"><input class="prod_code" type="hidden" name="prod_code[]" value="'+this.value+'" />' +
                    '<input class="quantity" type="hidden" name="quantity[]" value="'+qty+'" /></div>');
            } else {
                //btn.attr("disabled",!checkboxes.is(":checked"));
                //.parentNode.removeChild(element);
                //$('.prod_code:input[value="'+this.value+'"]').remove();
                $('.prod_code:input[value="'+this.value+'"]').parent('div').remove();
            }
        });
        $("tbody").delegate("#checkbox1", "change", function(){
            var tr = $(this).parent().parent();
            var qty = tr.find('#quantity').val();
            if($(this).prop('checked')){
                //btn.removeAttr('disabled');
                $('#replenish').append('<div class="new_item1"><input class="prod_code1" type="hidden" name="prod_code1[]" value="'+this.value+'" />' +
                    '<input class="quantity1" type="hidden" name="quantity1[]" value="'+qty+'" /></div>');
            } else {
                $('.prod_code1:input[value="'+this.value+'"]').parent('div').remove();
            }
        });

    });
    $(document).on('click','#view-qty', function () {
        var prod = $(this).data('prod');
        $.ajax({
            url: "inventory_analysis/" + prod,
            beforeSend: function () {
                $('#main-spinner').fadeIn();
            },
            success: function (data) {
                //var data = jQuery.parseJSON(output);
                $('#main-spinner').fadeOut();
                $('#ul-itm').html('');
                $.each(data, function (index, value) {
                    $('#md-title').text(value.inventory.name);
                    $('#ul-itm').append('<tr> ' +
                        '<td><small>'+ value.branch.name +'</small></td> ' +
                        '<td><small>'+ value.quantity +'</small></td> ' +
                        '</tr>');
                    //output.push({ id: value.inventory.code, text : value.inventory.name});
                });

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " " + thrownError);
            }
        });
    });
    $('input:radio[name=optCustType]').change(function () {
        var btn = $(this).val();
        if(btn=='all'){
            //$('#cust_name').removeAttr('required');
            $('#cust_name').prop('disabled', true).trigger("chosen:updated");
            $('.chosen-single span').html('Select Branch');
        }else if(btn=='branch'){
            $('#cust_name').prop('disabled', false).trigger("chosen:updated");
        }
    });
    @if (session('status'))
        $(document).ready(function () {
            toastr.success("{{ session('status') }}");
        });
    @endif
</script>
@endpush