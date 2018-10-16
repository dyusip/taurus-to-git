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
            <div class="row">
                <div class="col-md-12">
                    <div class="wrapper wrapper-content animated fadeInUp">

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Sales Report</h5>
                                <!--<div class="ibox-tools">
                                    <a href="" class="btn btn-primary btn-xs">Create new project</a>
                                </div>-->
                            </div>
                            <div class="ibox-content">
                                <div class="btn-group">
                                    <button class="btn btn-white btn-sm previous" id="prev" onclick="prevMonth()"><i class="fa fa-chevron-left"></i></button>
                                    <span class="current"></span>
                                    <button class="btn btn-white btn-sm next" id="next" onclick="nextMonth()"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <button type="button" id="btn-month" class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled" disabled="disabled">{{ Carbon\Carbon::now()->format('F Y') }}</button>
                                <input type="hidden" id="dt-month" value="{{ Carbon\Carbon::now()->format('Y-n') }}">
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-7 b-r">
                                        <div id="morris-donut-chart"></div>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-hover no-margins">
                                            <thead>
                                            <tr>
                                                <th>Branch</th>
                                                <th>Goal</th>
                                                <th>Sales</th>
                                                <th>Perf</th>
                                            </tr>
                                            </thead>
                                            <tbody id="performance">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                    val.push({'label' : value.so_branch.name, 'value': value.total});
                    var goal = (Number(value.total) / 5000) * 100;
                    goal = Number.isInteger(goal)?goal:goal.toFixed(1);
                    $('#performance').append(" <tr> " +
                        "<td><small>"+ value.so_branch.name +"</small></td> " +
                        "<td width='20%'> <input type='text' class='form-control' id='goal' value='5000'></td> " +
                    "<td id='sales'>"+ value.total +"</td> " +
                    "<td class='text-navy'> <i class='fa fa-level-up'></i> <span id='perf'>"+ goal+"%</span> </td> " +
                    "</tr>")
                });
                console.log(val.length);
                var color = [];
                var pick = ['#87d6c6', '#54cdb4','#1ab394', '#54cdb4','#1ab394', '#54cdb4','#1ab394'];

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
    var month = new Array();
    month = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

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
        var perf = (Number(sales) / Number(goal)) * 100;
        perf = Number.isInteger(perf)?perf:perf.toFixed(1);
        tr.find('#perf').html(perf+"%");
    });

</script>
@endpush