@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Management.sidebar')

        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Management.header')

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Dashboard</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Dashboard</strong>
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
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="row">
                    <div class="col-md-12">


                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Sales Report</h5>
                                <!--<div class="ibox-tools">
                                    <a href="" class="btn btn-primary btn-xs">Create new project</a>
                                </div>-->
                            </div>
                            <div class="ibox-content">
                                <div class="btn-group">
                                    <button class="btn btn-white btn-sm previous" id="prev" onclick="prevMonth()"><i
                                                class="fa fa-chevron-left"></i></button>
                                    <span class="current"></span>
                                    <button class="btn btn-white btn-sm next" id="next" onclick="nextMonth()"><i
                                                class="fa fa-chevron-right"></i></button>
                                </div>
                                <button type="button" id="btn-month"
                                        class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled"
                                        disabled="disabled">{{ Carbon\Carbon::now()->format('F Y') }}</button>
                                <button type="button" id="btn-today"
                                        class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled">Today {{ Carbon\Carbon::now()->format('F d, Y') }}</button>
                                <input type="hidden" id="dt-month" value="{{ Carbon\Carbon::now()->format('Y-n') }}">
                                <div class="clearfix"></div>
                                <div class="row">

                                    <div class="col-md-7 b-r">
                                        <table class="table table-hover no-margins">
                                            <thead>
                                            <tr>
                                                <th>Branch</th>
                                                <th width="25%">Goal</th>
                                                <th>Sales</th>
                                                <th>Perf</th>
                                            </tr>
                                            </thead>
                                            <tbody id="performance">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-5">
                                        <div id="morris-donut-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--Financial--}}
                <div class="row">
                    <div class="col-md-7">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                {{--<span class="label label-info pull-right">Annual</span>--}}
                                <h5>Financial View</h5>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-hover no-margins">
                                    <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Inv Cost</th>
                                        <th>Inv Srp</th>
                                        <th>Margin</th>
                                        <th>% Margin</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        @php
                                            $margin = $item->total_srp - $item->total_cost;
                                            $perc = ($margin != 0)?($margin / $item->total_srp) * 100:0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <small>{{$item->branch->name}}</small>
                                            </td>
                                            {{--<td><small>Pending...</small></td>--}}
                                            <td>{!! Number_Format($item->total_cost,2)  !!}</td>
                                            <td>{!! Number_Format($item->total_srp,2)  !!}</td>
                                            <td>{!! Number_Format($margin,2)  !!}</td>
                                            <td>{!! round($perc) ."%" !!}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    {{--Statistical--}}
                    <div class="col-md-5">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                {{--<span class="label label-info pull-right">Annual</span>--}}
                                <h5>Statistical View</h5>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-hover no-margins">
                                    <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Inv Volume</th>
                                        <th># of Items</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($stats as $stat)
                                        <tr>
                                            <td>
                                                <small>{{ $stat->branch->name }}</small>
                                            </td>
                                            {{--<td><small>Pending...</small></td>--}}
                                            <td>{!! Number_Format($stat->total_qty)  !!}</td>
                                            <td>{!! Number_Format($stat->total_count)  !!}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                {{--<span class="label label-info pull-right">Annual</span>--}}
                                <h5>Cost and SRP Min-Max View</h5>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-hover no-margins">
                                    <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Min Cost</th>
                                        <th>Max Cost</th>
                                        <th>Min SRP</th>
                                        <th>Max SRP</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($minmaxes as $minmax)
                                        <tr>
                                            <td>
                                                <small>{{$minmax->branch->name}}</small>
                                            </td>
                                            <td>{!! Number_Format($minmax->min_cost, 2)  !!}</td>
                                            <td>{!! Number_Format($minmax->max_cost, 2)  !!}</td>
                                            <td>{!! Number_Format($minmax->min_srp, 2)  !!}</td>
                                            <td>{!! Number_Format($minmax->max_srp, 2)  !!}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
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
    <!-- /#wrapper -->

    <!-- jQuery -->

@endsection
@push('styles')
<link href="{{  asset('css/plugins/morris/morris-0.4.3.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<!-- Morris -->
<script src="{{  asset('js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
<script src="{{  asset('js/plugins/morris/morris.js') }}"></script>

<!-- Morris demo data-->
{{--<script src="{{  asset('js/demo/morris-demo.js') }}"></script>--}}

<script>
    $(document).ready(function () {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() ,1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        Donut_chart(first,last)
    });
    function Donut_chart(first, last) {
        $.ajax({
            url : '/home/salesreport/'+first+"/"+last,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function (data) {
                $('#main-spinner').fadeOut();
                var val = [];
                $('#performance').html("");
                $.each(data, function (index, value) {
                    val.push({'label' : value.so_branch.name, 'value': value.total.toFixed(1)});
                    var goal = (Number(value.total) / value.so_branch.goal) * 100;
                    goal = Number.isInteger(goal)?goal:goal.toFixed(1);
                    $('#performance').append(" <tr> " +
                        "<td><small>"+ value.so_branch.name +"</small></td> " +
                        "<td width='20%'> <input type='text' class='form-control' id='goal' value='"+value.so_branch.goal+"'></td> " +
                        "<td id='sales'>"+ formatDollar(value.total) +"</td> " +
                        "<td class='text-navy'> <i class='fa fa-level-up'></i> <span id='perf'>"+ Math.round(goal)+"%</span> </td> " +
                        "</tr>")
                });
                console.log(val.length);
                var color = [];
                var pick = ['#87d6c6', '#54cdb4','#1ab394', '#37afbd',
                    '#53b5ad', '#16acba','#47c5d1','#42f4d7','#37725c',
                    '#106344','#9bf2e6','#10e8ca','#5dc5e2','#94dbef',
                    '#94efdd','#509184','#26574d','#268c80','#05a18e',
                    '#11bdbd','#3eadad','#19e3bb','#104f4f'];

                for (var i = 0; i < val.length;i++) {
                    color.push(pick[i])
                }
                $('#morris-donut-chart').empty();
                Morris.Donut({
                    element: 'morris-donut-chart',
                    data: val,
                    resize: true,
                    colors: color
                });
            }
        });
    }
    function formatDollar(num) {
        var p = num.toFixed(2).split(".");
        return "₱" + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return  num=="-" ? acc : num + (i && !(i % 3) ? "," : "") + acc;
            }, "") + "." + p[1];
    }

    var month = new Array();
    month = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    $(document).on('click','#btn-today',function () {
        var today = new Date();
        var date = (today.getMonth()+1)+'-'+today.getDate()+'-'+today.getFullYear();
        Donut_chart(date,date)
    });

    function prevMonth() {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() - 1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        $('#dt-month').val(d.getFullYear() + "-"+ (d.getMonth() + 1));
        $('#btn-month').text(month[d.getMonth()]+" "+d.getFullYear());
        Donut_chart(first,last)
    }
    function nextMonth() {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() + 1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        $('#dt-month').val(d.getFullYear() + "-"+ (d.getMonth() + 1));
        $('#btn-month').text(month[d.getMonth()]+" "+d.getFullYear());
        Donut_chart(first,last)
    }
    $('tbody').delegate('#goal','keyup',function () {
        var tr = $(this).parent().parent();
        var goal = $(this).val();
        var sales = tr.find('#sales').text();
        sales = Number(sales.replace(/[^0-9\.]+/g,""));
        var perf = (Number(sales) / Number(goal)) * 100;
        perf = Number.isInteger(perf)?perf:perf.toFixed(1);
        tr.find('#perf').html(Math.round(perf)+"%");
    });

</script>
@endpush