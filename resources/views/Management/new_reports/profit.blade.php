@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Management.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Management.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Profit Margin Report</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>
                            <a>Taurus</a>
                        </li>
                        <li class="active">
                            <strong>Profit Margin Report</strong>
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
                            <form role="form" action="/profit" method="post" name="myform" id="myform">
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
                                                <div class="col-md-4"><select data-placeholder="Choose a Branch..." class="select2_demo_1 form-control"  tabindex="2" name="branch" id = "cust_name" required {{ (@$request->optCustType == 'all' || @$request->optCustType == "") ? 'disabled':'' }}>
                                                        <option value="">Select Branch</option>
                                                        @foreach($branches as $branch)
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
                                    <button data-toggle="tooltip" title="Print"  class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/profit/print">
                                        <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                    </button>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="tbl-salesreport"
                                               data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th data-hide="phone" width="9%">BRANCH</th>
                                                <th data-hide="phone" width="9%">ITEM CODE</th>
                                                <th data-hide="phone" width="23%">NAME</th>
                                                {{-- <th data-hide="phone" width="9%">CATEGORY</th>--}}
                                                <th data-hide="phone,tablet" width="5%">COST</th>
                                                <th data-hide="phone,tablet" width="5%">SRP</th>
                                                <th data-hide="phone,tablet" width="5%">QTY</th>
                                                <th data-hide="phone,tablet" width="5%">PRICE</th>
                                                <th data-hide="phone" width="5%">LESS</th>
                                                <th data-hide="phone,tablet" width="5%">TOTAL COST</th>
                                                <th class="text-center" width="8%">SALES</th>
                                                <th class="text-center" width="8%">PROFIT</th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            @if(isset($sales))
                                                @php $total_cost = 0; @endphp
                                                @php $total = 0; @endphp
                                                @php $tot_profit = 0; @endphp
                                                @foreach($sales as $sale)
                                                    @php
                                                        $profit = $sale->total_amount - $sale->total_cost;
                                                        $less = Number_Format($sale->less, 0);
                                                        $branch = ($sale->branch=="")?'All Branch':$sale->branch;
                                                    @endphp
                                                        <tr>
                                                            <td>{{ $branch }}</td>
                                                            <td>{{ $sale->sod_prod_code }}</td>
                                                            <td>{{ $sale->sod_prod_name }}</td>
                                                            <td>{!! Number_Format($sale->cost,2) !!}</td>
                                                            <td>{!! Number_Format($sale->srp,2) !!}</td>
                                                            <td>{{ $sale->total_qty}}</td>
                                                            <td>{!! Number_Format($sale->price,2) !!}</td>
                                                            <td>{{ $less }}</td>
                                                            <td>{!! Number_Format($sale->total_cost,2) !!}</td>
                                                            <td>{!! Number_Format($sale->total_amount,2) !!}</td>
                                                            <td>{!! Number_Format($profit,2) !!}</td>
                                                        </tr>
                                                        @php
                                                            $total_cost += $sale->total_cost;
                                                            $total += $sale->total_amount;
                                                            $tot_profit += $profit;
                                                        @endphp
                                                @endforeach
                                                @php
                                                    $total_cost = Number_Format(@$total_cost,2);
                                                    $total = Number_Format(@$total,2);
                                                    $tot_profit = Number_Format(@$tot_profit,2);
                                                @endphp
                                            @endif
                                            </tbody>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <th class="text-right" colspan="8">TOTAL AMOUNT</th>
                                                <th>{{ @$total_cost }}</th>
                                                <th>{{ @$total }}</th>
                                                <th>{{ @$tot_profit }}</th>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </form>
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
</script>
@endpush