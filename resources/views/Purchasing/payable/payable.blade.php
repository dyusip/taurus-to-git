@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Taurus Payable Details</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>
                        <li>
                            <a>Taurus</a>
                        </li>
                        <li class="active">
                            <strong>Taurus Payable Details</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <form role="form" action="/payable" method="post" name="myform" id="myform">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content text-center p-md">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-horizontal">
                                                <div class="form-inline">
                                                    <label class="col-md-1 control-label">From</label>
                                                    <div class="controls">
                                                        <div class="col-md-3  form-group ">
                                                            <div class='input-group date' id='datepicker'>
                                                                <input type="text" class="form-control" autocomplete="off"  id="dateFrom"  required
                                                                       name="start" value="{{ (isset($request->start))?$request->start:date('m/d/Y') }}">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="control-inline">
                                                    <label class="col-md-1 control-label">To</label>
                                                    <div class="controls">
                                                        <div class="col-md-3  form-group ">
                                                            <div class='input-group date' id='datepicker2'>
                                                                <input class="form-control" autocomplete="off" type="text"  id="dateTo" required
                                                                       name="end" value="{{ (isset($request->end))?$request->end:date('m/d/Y') }}">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="control-inline">
                                                    <label class="col-sm-1 control-label"></label>
                                                    <div class="controls">
                                                        <div class="col-md-3 xdisplay_inputx form-group has-feedback">
                                                            <button class="btn btn-primary btn-w-m" type="submit" name="viewButton" onclick="this.form.submit()">
                                                                <span aria-hidden="true" class="fa fa-search"></span>
                                                                View Payable
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <!--<h5>Information</h5>-->
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button data-toggle="tooltip" title="Print"  class="btn btn-primary btn-sm dim" name="CEOprintForm" formaction="/payable/print">
                                                <span aria-hidden="true" class="fa fa-print fa-5x"></span>
                                            </button>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover"  id="tbl-payable">

                                                    <thead>
                                                    <tr>
                                                        <th>PO#</th>
                                                        <th>PO Date</th>
                                                        <th>Rec Date</th>
                                                        <th>SI #</th>
                                                        <th>Vendor Name</th>
                                                        <th>Term</th>
                                                        <th>Due Date</th>
                                                        <th>PO Amount</th>
                                                        <th>Rec Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($items))
                                                        @php $total_amount = 0; $total_po_amnt = 0; @endphp
                                                        @foreach($items as $item)
                                                            @foreach($item->po_re_header as $receiving)
                                                                @php $total = 0; @endphp
                                                                @if(!is_null($receiving->pop_header))
                                                                    @php
                                                                        $amount = $receiving->pop_header->ph_rembal;
                                                                        $total += $amount;
                                                                    @endphp
                                                                @else
                                                                    @foreach($receiving->re_detail as $story)
                                                                        @foreach($item->po_detail as $key)
                                                                            @if($key->prod_code == $story->rd_prod_code)
                                                                                @php
                                                                                    $price = $key->prod_price;
                                                                                    $less = $key->prod_less;
                                                                                    $amount = ($price * $story->rd_prod_qty - (($price * $story->rd_prod_qty) * $less/100));
                                                                                    $total += $amount;
                                                                                @endphp
                                                                            @endif
                                                                        @endforeach
                                                                    @endforeach
                                                                @endif
                                                                @php
                                                                    $rec_date = \Carbon\Carbon::createFromFormat('Y-m-d', $receiving->rh_date)->format('m/d/Y');
                                                                    $duedate = \Carbon\Carbon::parse($receiving->rh_date)->addDays($item->term);
                                                                    $duedate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $duedate)->format('m/d/Y');
                                                                    $total_amount += $total;
                                                                    $total_po_amnt += doubleval(str_replace(",", "", $receiving->re_po_header->total));
                                                                @endphp
                                                                <tr {!!(date('m/d/Y') >= $duedate)?'class="danger"':''!!} >
                                                                    <td>{{$item->po_code}}</td>
                                                                    <td>{{$item->po_date}}</td>
                                                                    <td>{{$rec_date}}</td>
                                                                    <td>{{$receiving->rh_si_no}}</td>
                                                                    <td>{{$item->supplier->name}}</td>
                                                                    <td>{{$item->term}}</td>
                                                                    <td>{{$duedate}}</td>
                                                                    <td>{{ $receiving->re_po_header->total }}</td>
                                                                    <td>{{Number_Format($total,2)}}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endif

                                                    </tbody>
                                                    <tr>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <th class="text-right" colspan="7">TOTAL AMOUNT</th>
                                                        <th>{{Number_Format(@$total_po_amnt,2)}}</th>
                                                        <th>{{Number_Format(@$total_amount,2)}}</th>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.table-responsive -->
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
        $('#tbl-payable').DataTable({
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
<script type="text/javascript">
    $(function () {
        $('#datepicker').datepicker();
        $('#datepicker2').datepicker();

    });
</script>
@endpush