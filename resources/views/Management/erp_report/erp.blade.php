@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Management.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Management.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Taurus Enteprise Block Box Report</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ url('\home') }}">Home</a>
                        </li>
                        <li>
                            <a>Taurus</a>
                        </li>
                        <li class="active">
                            <strong>Taurus Enteprise Block Box Report</strong>
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
                            <form role="form" action="/erp_report" method="post" name="myform" id="myform">
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
                                                            <input type="text" class="input-sm form-control" name="start" value="{{ (isset($request->start))?$request->start:date('m/01/Y') }}"/>
                                                            <span class="input-group-addon">to</span>
                                                            <input type="text" class="input-sm form-control" name="end" value="{{ (isset($request->end))?$request->end:date('m/t/Y') }}" />
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
                                    <button data-toggle="tooltip" title="Print"  class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/erp_report/print">
                                        <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                    </button>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="tbl-transfer"
                                               data-page-size="15">
                                            <thead>
                                            <tr>
                                                <th width="10%">{!! @$request->optCustType=='all'?'ALL BRANCH':(!isset($request->optCustType))?'BRANCH':@$chosen_branch !!}</th>
                                                <th width="10%">COST</th>
                                                <th width="10%">SRP</th>
                                                {{--<th width="10%">RECIPIENT</th>--}}
                                                {{-- <th width="10%">QTY</th>
                                                 <th width="10%">VALUE</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody id="order-tbl" class="tooltip-demo">
                                            {{--@if(isset($items))--}}
                                                <tr>
                                                    <td>Beg Inv</td>
                                                    <td>{!! Number_Format(@$inv_beg->ip_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$inv_beg->ip_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>CW Deliveries</td>
                                                    <td>{!! Number_Format(@$cw->total_cw_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$cw->total_cw_srp,2) !!}</td>
                                                   {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Transfer In</td>
                                                    <td>{!! Number_Format(@$tf_in->total_tf_in_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$tf_in->total_tf_in_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Sales</td>
                                                    <td>{!! Number_Format(@$sales->total_so_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$sales->total_so_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Sales Return</td>
                                                    <td>{!! Number_Format(@$sales_return->total_so_return_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$sales_return->total_so_return_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Transfer out</td>
                                                    <td>{!! Number_Format(@$tf_out->total_tf_out_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$tf_out->total_tf_out_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Stock Returns</td>
                                                    <td>{!! Number_Format(@$return->total_return_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$return->total_return_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                {{--<tr>
                                                    <td>Discount</td>
                                                    <td>{!! Number_Format(@$sales_disc->total_so_disc_cost,2) !!}%</td>
                                                    <td>{!! Number_Format(@$sales_disc->total_so_disc_srp,2) !!}</td>
                                                    --}}{{-- <td>{{ $item->tf_to_branch->name }}</td>--}}{{--
                                                </tr>--}}
                                                <tr>
                                                    <td>Positive Discount</td>
                                                    <td>{!! Number_Format(@$sales_disc->pos_disc_perc,2) !!}%</td>
                                                    <td>{!! Number_Format(@$sales_disc->pos_disc,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Negative Discount</td>
                                                    <td>{!! Number_Format(@$sales_disc->neg_disc_perc,2) !!}%</td>
                                                    <td>{!! Number_Format(@$sales_disc->neg_disc,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Miscellaneous IN</td>
                                                    <td>{!! Number_Format(@$misc_in->misc_in_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$misc_in->misc_in_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Miscellaneous OUT</td>
                                                    <td>{!! Number_Format(@$misc_out->misc_out_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$misc_out->misc_out_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                                <tr>
                                                    <td>Positive Cost and SRP Adjustment</td>
                                                    <td>{!! Number_Format(@$pos_adj_cost + @$sales->total_pos_prch_cost + @$cw->total_pos_prch_cost + @$tf_out->total_pos_prch_cost + @$misc_in->total_pos_prch_cost + @$misc_out->total_pos_prch_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$pos_adj_srp + @$sales->total_pos_prch_srp + @$cw->total_pos_prch_srp + @$tf_out->total_pos_prch_srp + @$misc_in->total_pos_prch_srp + @$misc_out->total_pos_prch_srp,2) !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>Negative Cost and SRP Adjustment</td>
                                                    <td>{!! Number_Format(@$neg_adj_cost + @$sales->total_neg_prch_cost + @$cw->total_neg_prch_cost + @$tf_out->total_neg_prch_cost + @$misc_in->total_neg_prch_cost + @$misc_out->total_neg_prch_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$neg_adj_srp + @$sales->total_neg_prch_srp + @$cw->total_neg_prch_srp + @$tf_out->total_neg_prch_srp + @$misc_in->total_neg_prch_srp + @$misc_out->total_neg_prch_srp,2) !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>End Inv</td>
                                                    <td>{!! Number_Format(@$end_inv->ip_cost,2) !!}</td>
                                                    <td>{!! Number_Format(@$end_inv->ip_srp,2) !!}</td>
                                                    {{-- <td>{{ $item->tf_to_branch->name }}</td>--}}
                                                </tr>
                                            {{--@endif--}}
                                            </tbody>


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
        $('#tbl-transfer').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "bSort" : false,
            "pageLength": 25,
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
        $('#tbl-transfer2').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "bSort" : false,
            "pageLength": 25,
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